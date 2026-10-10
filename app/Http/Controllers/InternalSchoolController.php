<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
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
        if ($principal->isPlatformAdmin()) {
            throw ValidationException::withMessages(['principal_email' => 'Platform administrators cannot hold a school role. Choose a separate principal account.']);
        }
        try {
            DB::transaction(function () use ($validated, $principal): void {
                $principal = User::query()->whereKey($principal->getKey())->lockForUpdate()->firstOrFail();
                if ($principal->schools()->wherePivotIn('role', User::SingleSchoolRoles)->exists()) {
                    throw ValidationException::withMessages(['principal_email' => 'This account already has a role restricted to one school. Choose a separate principal account.']);
                }

                $school = School::create(['name' => $validated['name']]);
                $school->users()->attach($principal, ['role' => 'principal']);
            });
        } catch (UniqueConstraintViolationException $exception) {
            if (! School::where('name', $validated['name'])->exists()) {
                throw $exception;
            }

            throw ValidationException::withMessages(['name' => 'A school with this name already exists.']);
        }

        return redirect()->route('internal.schools.index')->with('status', 'School created and principal assigned.');
    }
}
