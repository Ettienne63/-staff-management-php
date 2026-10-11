<?php

namespace App\Http\Controllers;

use App\Models\OnboardingAudit;
use App\Models\PortalInvitation;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvitationController extends Controller
{
    private function resolve(string $token): PortalInvitation
    {
        $invitation = PortalInvitation::where('token_hash', hash('sha256', $token))->with('school')->firstOrFail();
        abort_unless($invitation->isUsable(), 410, 'This invitation has expired, was used, or was revoked. Ask the school for a new invitation.');

        return $invitation;
    }

    public function show(Request $request, string $token): View
    {
        $invitation = $this->resolve($token);
        $existingAccount = User::whereRaw('LOWER(email) = ?', [$invitation->email])->exists();
        if ($existingAccount && ! $request->user()) {
            $request->session()->put('url.intended', route('invitations.show', $token));
        }

        return view('auth.invitation', compact('invitation', 'token', 'existingAccount'));
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->resolve($token);
        $data = $request->user() ? [] : $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:72', 'confirmed', Password::min(12)->mixedCase()->numbers()],
        ]);
        try {
            $user = DB::transaction(function () use ($request, $invitation, $data): User {
                $school = School::whereKey($invitation->school_id)->lockForUpdate()->firstOrFail();
                $invitation = PortalInvitation::whereKey($invitation->id)->lockForUpdate()->firstOrFail();
                abort_unless($invitation->isUsable(), 410);
                $roles = $invitation->type === 'student' ? User::LeadershipRoles : User::ManagementRoles;
                abort_unless($school->users()->whereKey($invitation->created_by)->wherePivotIn('role', $roles)->exists(), 410);
                $existing = User::whereRaw('LOWER(email) = ?', [$invitation->email])->lockForUpdate()->first();
                if ($existing && (! $request->user() || $request->user()->id !== $existing->id)) {
                    throw ValidationException::withMessages(['email' => 'Sign in as the invited account before accepting. An invitation never changes an existing password.']);
                }
                if ($request->user() && strtolower($request->user()->email) !== $invitation->email) {
                    abort(403, 'This invitation belongs to a different account.');
                }
                if ($invitation->type === 'student' && $existing) {
                    throw ValidationException::withMessages(['email' => 'Student activation requires a separate unused email. Ask the school for a new invitation.']);
                }
                if ($existing && ! $existing->usesFamilyPortal()) {
                    abort(403);
                }
                $user = $existing ?? User::create(['name' => $data['name'], 'email' => $invitation->email, 'password' => $data['password']]);
                $user->forceFill(['email_verified_at' => now()])->save();
                $student = null;
                if ($invitation->type === 'student') {
                    $student = $school->students()->whereKey($invitation->student_id)->lockForUpdate()->firstOrFail();
                    abort_if($student->user_id !== null || ! $student->guardians()->wherePivotNull('revoked_at')->exists(), 410);
                    abort_if($user->schools()->exists() || $user->student()->exists() || $user->children()->exists(), 403);
                    $student->update(['user_id' => $user->id]);
                    $user->schools()->attach($school, ['role' => 'student']);
                } else {
                    $user->schools()->syncWithoutDetaching([$school->id => ['role' => 'guardian']]);
                }
                $invitation->update(['accepted_at' => now()]);
                OnboardingAudit::create([
                    'school_id' => $school->id, 'student_id' => $student?->id, 'actor_id' => $user->id,
                    'action' => 'invitation.accepted', 'context' => ['invitation_id' => $invitation->id, 'type' => $invitation->type],
                ]);

                return $user;
            });
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['email' => 'This email now has an account. Sign in or ask the school for a new invitation.']);
        }
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()->route($invitation->type === 'student' ? 'schools.workspace' : 'family.dashboard',
            $invitation->type === 'student' ? ['school' => $invitation->school_id] : []);
    }
}
