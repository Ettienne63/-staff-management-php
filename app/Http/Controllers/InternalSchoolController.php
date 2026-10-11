<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InternalSchoolController extends Controller
{
    public function index(): View
    {
        return view('internal.schools', ['schools' => School::query()
            ->select(['id', 'name', 'public_email', 'public_phone', 'website', 'created_at'])
            ->withExists(['users as leadership_assigned' => fn (Builder $query) => $query->where('school_user.role', 'principal')
                ->where('users.email', '!=', config('app.platform_admin_email'))])
            ->orderBy('name')->paginate(20)]);
    }

    public function create(): View
    {
        return view('internal.create-school');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->publicDetailsRules() + [
            'name' => ['required', 'string', 'max:255', 'unique:schools,name'],
            'principal_email' => ['required', 'email', 'max:255'],
        ]);
        $principal = User::where('email', $validated['principal_email'])->first();
        if (! $principal) {
            throw ValidationException::withMessages(['principal_email' => 'Use the email of an existing account. Leadership signup is not available yet.']);
        }
        if ($principal->isPlatformAdmin()) {
            throw ValidationException::withMessages(['principal_email' => 'Platform administrators cannot hold a school role. Choose a separate principal account.']);
        }
        try {
            DB::transaction(function () use ($request, $validated, $principal): void {
                $principal = User::query()->whereKey($principal->getKey())->lockForUpdate()->firstOrFail();
                if ($principal->schools()->wherePivotIn('role', User::SingleSchoolRoles)->exists()) {
                    throw ValidationException::withMessages(['principal_email' => 'This account already has a role restricted to one school. Choose a separate principal account.']);
                }

                $school = School::create(collect($validated)->except('principal_email')->all());
                $school->users()->attach($principal, ['role' => 'principal']);
                DB::table('platform_activities')->insert([
                    'actor_id' => $request->user()->id, 'school_id' => $school->id, 'action' => 'school.created',
                    'details' => json_encode(['name' => $school->name], JSON_THROW_ON_ERROR), 'created_at' => now(),
                ]);
            });
        } catch (UniqueConstraintViolationException $exception) {
            if (! School::where('name', $validated['name'])->exists()) {
                throw $exception;
            }

            throw ValidationException::withMessages(['name' => 'A school with this name already exists.']);
        }

        return redirect()->route('internal.schools.index')->with('status', 'School created and principal assigned.');
    }

    public function edit(School $school): View
    {
        return view('internal.edit-school', compact('school'));
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        $data = $request->validate($this->publicDetailsRules() + [
            'name' => ['required', 'string', 'max:255', Rule::unique('schools', 'name')->ignore($school->id)],
        ]);
        try {
            DB::transaction(function () use ($request, $school, $data): void {
                $school = School::whereKey($school->id)->lockForUpdate()->firstOrFail();
                $before = $school->only(['name', 'public_email', 'public_phone', 'website']);
                $school->fill($data);
                if (! $school->isDirty()) {
                    return;
                }
                $school->save();
                DB::table('platform_activities')->insert([
                    'actor_id' => $request->user()->id, 'school_id' => $school->id, 'action' => 'school.updated',
                    'details' => json_encode(['before' => $before, 'after' => $school->only(array_keys($before))], JSON_THROW_ON_ERROR),
                    'created_at' => now(),
                ]);
            });
        } catch (UniqueConstraintViolationException $exception) {
            if (! School::where('name', $data['name'])->whereKeyNot($school->id)->exists()) {
                throw $exception;
            }
            throw ValidationException::withMessages(['name' => 'A school with this name already exists.']);
        }

        return redirect()->route('internal.schools.index')->with('status', 'Public school details updated.');
    }

    /** @return array<string, list<string>> */
    private function publicDetailsRules(): array
    {
        return [
            'public_email' => ['nullable', 'email', 'max:255'],
            'public_phone' => ['nullable', 'string', 'max:40'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }
}
