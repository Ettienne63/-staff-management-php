<?php

namespace App\Http\Controllers;

use App\Models\ChildRegistration;
use App\Models\OnboardingAudit;
use App\Models\PortalInvitation;
use App\Models\School;
use App\Models\User;
use App\PortalInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SchoolOnboardingController extends Controller
{
    private function authorizeRole(Request $request, School $school, bool $leadershipOnly = false): void
    {
        abort_unless($school->users()->whereKey($request->user()->id)
            ->wherePivotIn('role', $leadershipOnly ? User::LeadershipRoles : User::ManagementRoles)->exists(), 403);
    }

    public function inviteGuardian(Request $request, School $school, PortalInvitationService $invitations): RedirectResponse
    {
        $this->authorizeRole($request, $school);
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $invitations->issue($school, $request->user(), $data['email'], 'guardian');

        return redirect()->route('schools.section', [$school, 'accounts'])->with('status', 'Parent invitation sent. It expires in 7 days.');
    }

    public function review(Request $request, School $school, int $registration, PortalInvitationService $invitations): RedirectResponse
    {
        $this->authorizeRole($request, $school, true);
        $data = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'student_id' => ['nullable', 'integer'],
            'review_note' => ['required_if:decision,reject', 'nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);
        DB::transaction(function () use ($request, $school, $registration, $data, $invitations): void {
            School::whereKey($school->id)->lockForUpdate()->firstOrFail();
            $this->authorizeRole($request, $school, true);
            $registration = ChildRegistration::where('school_id', $school->id)->whereKey($registration)->lockForUpdate()->firstOrFail();
            if ($registration->status !== 'pending') {
                throw ValidationException::withMessages(['decision' => 'This request has already been reviewed.']);
            }
            $student = null;
            if ($data['decision'] === 'approve') {
                $guardian = User::whereKey($registration->guardian_user_id)->lockForUpdate()->firstOrFail();
                if (! $guardian->usesFamilyPortal() || ! $guardian->schools()
                    ->whereKey($school->id)->wherePivot('role', 'guardian')->exists()) {
                    throw ValidationException::withMessages(['decision' => 'The parent must have access to submit requests at this school and use a separate family account.']);
                }
                if (! empty($data['student_id'])) {
                    $student = $school->students()->whereKey($data['student_id'])->lockForUpdate()->firstOrFail();
                } else {
                    if ($school->students()->whereRaw('LOWER(first_name) = ?', [strtolower($registration->first_name)])
                        ->whereRaw('LOWER(last_name) = ?', [strtolower($registration->last_name)])
                        ->whereDate('date_of_birth', $registration->date_of_birth)->exists()) {
                        throw ValidationException::withMessages(['student_id' => 'A possible matching student already exists. Verify the identity and select the existing record.']);
                    }
                    $student = $school->students()->create($registration->only(['first_name', 'last_name', 'date_of_birth', 'grade']));
                }
                if ($guardian->hasMatchingChildAtAnotherSchool($school->id, $student->first_name, $student->last_name, $student->date_of_birth->format('Y-m-d'))) {
                    throw ValidationException::withMessages(['student_id' => 'School enrollment needs review before creating or linking another student record.']);
                }
                $student->guardians()->syncWithoutDetaching([$registration->guardian_user_id => [
                    'verified_by' => $request->user()->id, 'verified_at' => now(), 'revoked_at' => null,
                ]]);
                if (! empty($data['email']) && $student->user_id === null) {
                    $invitations->issue($school, $request->user(), $data['email'], 'student', $student);
                }
            }
            $registration->update([
                'status' => $data['decision'] === 'approve' ? 'approved' : 'rejected', 'student_id' => $student?->id,
                'reviewed_by' => $request->user()->id, 'reviewed_at' => now(), 'review_note' => $data['review_note'] ?? null,
            ]);
            OnboardingAudit::create([
                'school_id' => $school->id, 'student_id' => $student?->id, 'actor_id' => $request->user()->id,
                'action' => 'child.'.$registration->status,
                'context' => ['registration_id' => $registration->id, 'guardian_user_id' => $registration->guardian_user_id],
            ]);
        });

        return redirect()->route('schools.section', [$school, 'students'])->with('status', 'Child request reviewed.');
    }

    public function inviteStudent(Request $request, School $school, int $student, PortalInvitationService $invitations): RedirectResponse
    {
        $this->authorizeRole($request, $school, true);
        $student = $school->students()->whereKey($student)->firstOrFail();
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $invitations->issue($school, $request->user(), $data['email'], 'student', $student);

        return redirect()->route('schools.section', [$school, 'students'])->with('status', 'Student invitation sent. Parent and student accounts remain separate.');
    }

    public function revokeGuardian(Request $request, School $school, int $student, int $guardian): RedirectResponse
    {
        $this->authorizeRole($request, $school, true);
        DB::transaction(function () use ($request, $school, $student, $guardian): void {
            School::whereKey($school->id)->lockForUpdate()->firstOrFail();
            $this->authorizeRole($request, $school, true);
            $student = $school->students()->whereKey($student)->lockForUpdate()->firstOrFail();
            abort_unless($student->guardians()->whereKey($guardian)->wherePivotNull('revoked_at')->exists(), 404);
            $student->guardians()->updateExistingPivot($guardian, ['revoked_at' => now()]);
            OnboardingAudit::create([
                'school_id' => $school->id, 'student_id' => $student->id, 'actor_id' => $request->user()->id,
                'action' => 'guardian.revoked', 'context' => ['guardian_user_id' => $guardian],
            ]);
        });

        return redirect()->route('schools.section', [$school, 'students'])->with('status', 'Guardian access removed.');
    }

    public function revokeInvitation(Request $request, School $school, int $invitation): RedirectResponse
    {
        DB::transaction(function () use ($request, $school, $invitation): void {
            School::whereKey($school->id)->lockForUpdate()->firstOrFail();
            $invitation = PortalInvitation::where('school_id', $school->id)->whereKey($invitation)->lockForUpdate()->firstOrFail();
            $this->authorizeRole($request, $school, $invitation->type === 'student');
            if ($invitation->accepted_at !== null) {
                throw ValidationException::withMessages(['invitation' => 'This invitation was already accepted. Revoking a link does not remove an existing account.']);
            }
            $invitation->update(['revoked_at' => now()]);
            OnboardingAudit::create([
                'school_id' => $school->id, 'student_id' => $invitation->student_id, 'actor_id' => $request->user()->id,
                'action' => 'invitation.revoked', 'context' => ['invitation_id' => $invitation->id],
            ]);
        });

        return redirect()->route('schools.section', [$school, 'accounts'])->with('status', 'Invitation link revoked.');
    }
}
