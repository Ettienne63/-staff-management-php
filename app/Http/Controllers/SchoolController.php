<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request): View
    {
        $schools = $request->user()->schools()->wherePivotIn('role', ['principal', 'deputy', 'coach'])
            ->orderBy('name')->get();

        return view('dashboard', compact('schools'));
    }

    public function select(Request $request, School $school): RedirectResponse
    {
        $request->session()->put('school_id', $school->id);

        return redirect()->route('staff.index', $school);
    }

    public function legacyStaff(Request $request): RedirectResponse
    {
        $schools = $request->user()->schools()->wherePivotIn('role', ['principal', 'deputy', 'coach']);
        $school = (clone $schools)->whereKey($request->session()->get('school_id'))->first()
            ?? $schools->orderBy('schools.id')->first();

        return $school ? redirect()->route('staff.index', $school) : redirect()->route('dashboard');
    }
}
