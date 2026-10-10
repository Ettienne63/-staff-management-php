<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InternalSchoolController extends Controller
{
    public function index(): View
    {
        return view('internal.schools', ['schools' => School::orderBy('name')->paginate(20)]);
    }

    public function create(): View
    {
        return view('internal.create-school');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:schools,name'],
            'principal_email' => ['required', 'email', 'max:255'],
        ]);
        $principal = User::where('email', $validated['principal_email'])->first();
        if (! $principal) {
            throw ValidationException::withMessages(['principal_email' => 'Use the email of an existing account. Invitation signup is not available yet.']);
        }

        DB::transaction(function () use ($validated, $principal): void {
            $school = School::create(['name' => $validated['name']]);
            $school->users()->attach($principal, ['role' => 'principal']);
        });

        return redirect()->route('internal.schools.index')->with('status', 'School created and principal assigned.');
    }
}
