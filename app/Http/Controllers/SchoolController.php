<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request): View
    {
        $memberships = $request->user()->schools()->wherePivotIn('role', User::SchoolRoles);
        $leadershipSchool = (clone $memberships)->wherePivotIn('role', User::LeadershipRoles)->orderBy('schools.id')->first();
        $schools = $leadershipSchool
            ? collect([$leadershipSchool])
            : $memberships->orderBy('name')->get();

        return view('dashboard', compact('schools'));
    }

    public function select(Request $request, School $school): RedirectResponse
    {
        $request->session()->put('school_id', $school->id);

        return redirect()->route('schools.workspace', $school);
    }

    public function legacyStaff(Request $request): RedirectResponse
    {
        $schools = $request->user()->schools()->wherePivotIn('role', User::StaffRoles);
        $leadershipSchool = (clone $schools)->wherePivotIn('role', User::LeadershipRoles)->orderBy('schools.id')->first();
        if ($leadershipSchool) {
            return redirect()->route('staff.index', $leadershipSchool);
        }
        $school = (clone $schools)->whereKey($request->session()->get('school_id'))->first()
            ?? $schools->orderBy('schools.id')->first();

        return $school ? redirect()->route('staff.index', $school) : redirect()->route('dashboard');
    }
}
