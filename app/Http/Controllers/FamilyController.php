<?php

namespace App\Http\Controllers;

use App\Models\ChildRegistration;
use App\Models\OnboardingAudit;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FamilyController extends Controller
{
    private function authorizeFamily(Request $request): void
    {
        abort_if($request->user()->schools()->wherePivotIn('role', User::FamilySpaceRestrictedRoles)->exists(), 404);
    }

    public function index(Request $request, string $section = 'progress'): View
    {
        $this->authorizeFamily($request);

        return view('guardian.dashboard', [
            'section' => $section,
            'children' => $request->user()->children()->with('school')->orderBy('first_name')->get(),
            'schools' => $request->user()->schools()->wherePivot('role', 'guardian')->orderBy('name')->get(),
            'registrations' => ChildRegistration::where('guardian_user_id', $request->user()->id)->with('school')->latest()->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeFamily($request);
        $data = $request->validate([
            'school_id' => ['required', 'integer'],
            'first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:today'],
            'grade' => ['required', 'integer', 'between:1,12'],
            'relationship' => ['required', Rule::in(['parent', 'legal_guardian'])],
            'student_email' => ['nullable', 'email', 'max:255'],
        ]);
        DB::transaction(function () use ($request, $data): void {
            $school = School::whereKey($data['school_id'])->lockForUpdate()->firstOrFail();
            $guardian = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            abort_unless($request->user()->schools()->whereKey($school->id)->wherePivot('role', 'guardian')->exists(), 404);
            if ($guardian->hasMatchingChildAtAnotherSchool($school->id, $data['first_name'], $data['last_name'], $data['date_of_birth'])) {
                throw ValidationException::withMessages(['school_id' => 'A child with these details is already verified at another school. Ask your school to review enrollment; transfers are not available yet.']);
            }
            if (ChildRegistration::where('guardian_user_id', $request->user()->id)->where('school_id', $school->id)
                ->where('first_name', $data['first_name'])->where('last_name', $data['last_name'])
                ->whereDate('date_of_birth', $data['date_of_birth'])->where('status', 'pending')->exists()) {
                throw ValidationException::withMessages(['first_name' => 'A matching request is already waiting for school verification.']);
            }
            $registration = ChildRegistration::create($data + ['guardian_user_id' => $request->user()->id]);
            OnboardingAudit::create([
                'school_id' => $school->id, 'actor_id' => $request->user()->id,
                'action' => 'child.requested', 'context' => ['registration_id' => $registration->id],
            ]);
        });

        return redirect()->route('family.dashboard')->with('status', 'Your request is awaiting school verification.');
    }

    public function show(Request $request, Student $student): View
    {
        $this->authorizeFamily($request);
        $child = $request->user()->children()->whereKey($student->id)->with('school')->firstOrFail();

        return view('guardian.child', ['child' => $child]);
    }
}
