<?php

namespace App;

use App\Models\OnboardingAudit;
use App\Models\PortalInvitation;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Notifications\PortalInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PortalInvitationService
{
    public function canIssue(School $school, User $actor, string $type, ?Student $student = null): bool
    {
        if ($actor->isPlatformAdmin()) {
            return false;
        }

        $roles = $type === 'student' ? User::LeadershipRoles : User::ManagementRoles;
        if ($school->users()->whereKey($actor->id)->wherePivotIn('role', $roles)->exists()) {
            return true;
        }

        return $type === 'student' && $student && $student->school_id === $school->id
            && $actor->usesFamilyPortal()
            && $school->users()->whereKey($actor->id)->wherePivot('role', 'guardian')->exists()
            && $student->guardians()->whereKey($actor->id)->wherePivotNotNull('verified_at')->wherePivotNull('revoked_at')->exists();
    }

    public function issue(School $school, User $actor, string $email, string $type, ?Student $student = null): void
    {
        DB::transaction(function () use ($school, $actor, $email, $type, $student): void {
            School::whereKey($school->id)->lockForUpdate()->firstOrFail();
            abort_unless($this->canIssue($school, $actor, $type, $student), 403);
            $email = strtolower(trim($email));
            $existingUser = User::whereRaw('LOWER(email) = ?', [$email])->first();
            if ($type === 'student') {
                $student = $school->students()->whereKey($student?->id)->lockForUpdate()->firstOrFail();
                if ($student->user_id || ! $student->guardians()->wherePivotNull('revoked_at')->exists()) {
                    throw ValidationException::withMessages(['email' => 'Student activation needs a verified guardian and a student without an active login.']);
                }
                if ($existingUser) {
                    throw ValidationException::withMessages(['email' => 'Use the student’s own email that is not already attached to an account. Parent and student logins must be separate.']);
                }
            } elseif ($existingUser && ! $existingUser->usesFamilyPortal()) {
                throw ValidationException::withMessages(['email' => 'This account already uses a school or platform workspace. Use a separate parent account.']);
            }

            $previous = PortalInvitation::where('school_id', $school->id)->where('type', $type)
                ->whereNull('accepted_at')->whereNull('revoked_at');
            $type === 'student' ? $previous->where('student_id', $student->id) : $previous->where('email', $email);
            $previous->update(['revoked_at' => now()]);
            $token = Str::random(64);
            $invitation = PortalInvitation::create([
                'school_id' => $school->id, 'student_id' => $student?->id, 'created_by' => $actor->id,
                'email' => $email, 'type' => $type, 'token_hash' => hash('sha256', $token), 'expires_at' => now()->addDays(7),
            ]);
            OnboardingAudit::create([
                'school_id' => $school->id, 'student_id' => $student?->id, 'actor_id' => $actor->id,
                'action' => 'invitation.issued', 'context' => ['invitation_id' => $invitation->id, 'type' => $type],
            ]);
            DB::afterCommit(fn () => Notification::route('mail', $email)->notify(
                new PortalInvitationNotification(route('invitations.show', $token), $school->name, $type),
            ));
        });
    }
}
