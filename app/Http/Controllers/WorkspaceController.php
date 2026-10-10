<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
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
        ]);
    }

    public function section(Request $request, School $school, string $section): View
    {
        $role = $request->attributes->get('school_role');
        abort_if($section === 'accounts' && ! in_array($role, User::ManagementRoles, true), 403);
        abort_if($section === 'students' && $role === 'student', 403);
        abort_if($role === 'hr' && $section !== 'accounts', 403);

        [$title, $description, $emptyMessage] = match ($section) {
            'accounts' => ['Account administration', 'HR handles day-to-day access alongside school leadership.', 'Invitations, password recovery, and access changes are not available yet. Staff records do not create login accounts.'],
            'students' => ['Students', 'School student records will appear here once the roster workflow is available.', 'No student roster is connected yet. Student records will be separate from login accounts.'],
            'training' => [$role === 'student' ? 'My training' : 'Training', 'Activities, goals, and scheduled sessions in one place.', 'Training assignments and completion are not available yet.'],
            'progress' => [$role === 'student' ? 'My progress' : 'Progress', 'Follow participation, measured performance, and personal improvement.', 'No assessments are connected yet. Progress will appear once comparable results are recorded.'],
        };

        $view = $role === 'student' ? 'student.'.$section.'.index' : 'schools.'.$section.'.index';

        return view($view, compact('school', 'role', 'section', 'title', 'description', 'emptyMessage'));
    }
}
