<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->isPlatformAdmin()) {
            return redirect()->route('internal.schools.index');
        }

        if ($request->user()->usesFamilyPortal()) {
            return redirect()->route('family.dashboard');
        }

        $memberships = $request->user()->schools()->wherePivotIn('role', User::SchoolRoles);
        $singleSchool = (clone $memberships)->wherePivotIn('role', User::SingleSchoolRoles)->orderBy('schools.id')->first();
        $schools = $singleSchool
            ? collect([$singleSchool])
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
        abort_if($request->user()->isPlatformAdmin(), 404);
        $schools = $request->user()->schools()->wherePivotIn('role', User::StaffRoles);
        $leadershipSchool = (clone $schools)->wherePivotIn('role', User::SingleSchoolRoles)->orderBy('schools.id')->first();
        if ($leadershipSchool) {
            return redirect()->route('staff.index', $leadershipSchool);
        }
        $school = (clone $schools)->whereKey($request->session()->get('school_id'))->first()
            ?? $schools->orderBy('schools.id')->first();

        return $school ? redirect()->route('staff.index', $school) : redirect()->route('dashboard');
    }
}
