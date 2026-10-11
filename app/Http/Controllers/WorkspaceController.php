<?php

namespace App\Http\Controllers;

use App\Models\ChildRegistration;
use App\Models\PortalInvitation;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function index(Request $request, School $school): View
    {
        $role = $request->attributes->get('school_role');

        $view = match ($role) {
            'principal', 'deputy' => 'schools.leadership.dashboard',
            'hr' => 'schools.hr.dashboard',
            'coach' => 'schools.coach.dashboard',
            'student' => 'student.dashboard',
        };

        return view($view, [
            'school' => $school,
            'role' => $role,
            'roleLabel' => match ($role) {
                'principal' => 'Principal',
                'deputy' => 'Vice principal',
                'hr' => 'HR',
                'coach' => 'Coach',
                'student' => 'Student',
            },
            'staffCount' => in_array($role, User::ManagementRoles, true) ? $school->staffMembers()->count() : null,
            'student' => $role === 'student' ? $request->user()->student()->where('school_id', $school->id)->first() : null,
        ]);
    }

    public function section(Request $request, School $school, string $section): View
    {
        $role = $request->attributes->get('school_role');
        abort_if($section === 'accounts' && ! in_array($role, User::ManagementRoles, true), 403);
        abort_if($section === 'students' && $role === 'student', 403);
        abort_if($role === 'hr' && $section !== 'accounts', 403);

        if ($section === 'accounts') {
            $invitations = PortalInvitation::where('school_id', $school->id);
            if ($role === 'hr') {
                $invitations->where('type', 'guardian');
            }

            return view('schools.accounts.index', ['school' => $school, 'invitations' => $invitations->latest()->paginate(20)]);
        }
        if ($section === 'students') {
            $canVerify = in_array($role, User::LeadershipRoles, true);
            $students = $school->students()->orderBy('last_name');
            if ($canVerify) {
                $students->with(['guardians' => fn (BelongsToMany $query) => $query->wherePivotNull('revoked_at')]);
            }

            return view('schools.students.index', [
                'school' => $school, 'canVerify' => $canVerify, 'students' => $students->paginate(20),
                'registrations' => $canVerify ? ChildRegistration::where('school_id', $school->id)->where('status', 'pending')
                    ->with('guardian')->oldest()->paginate(10, ['*'], 'requests_page') : null,
                'existingStudents' => $canVerify ? $school->students()->orderBy('last_name')->get() : collect(),
            ]);
        }

        [$title, $description, $emptyMessage] = match ($section) {
            'training' => [$role === 'student' ? 'My training' : 'Training', 'Activities, goals, and scheduled sessions in one place.', 'Training assignments and completion are not available yet.'],
            'progress' => [$role === 'student' ? 'My progress' : 'Progress', 'Follow participation, measured performance, and personal improvement.', 'No assessments are connected yet. Progress will appear once comparable results are recorded.'],
        };

        $view = $role === 'student' ? 'student.'.$section.'.index' : 'schools.'.$section.'.index';

        return view($view, compact('school', 'role', 'section', 'title', 'description', 'emptyMessage'));
    }
}
