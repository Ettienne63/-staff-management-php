<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request, School $school): View
    {
        return view('staff.index', [
            'school' => $school,
            'staffMembers' => $school->staffMembers()->latest('id')->paginate(20),
            'canManage' => in_array($request->attributes->get('school_role'), ['principal', 'deputy'], true),
        ]);
    }

    public function create(Request $request, School $school): View
    {
        $this->authorizeManagement($request);

        return view('staff.form', ['school' => $school, 'staffMember' => null]);
    }

    public function store(StaffRequest $request, School $school): RedirectResponse
    {
        $school->staffMembers()->create($request->validated());

        return redirect()->route('staff.index', $school)->with('status', 'Staff member added.');
    }

    public function edit(Request $request, School $school, int $staff): View
    {
        $this->authorizeManagement($request);

        return view('staff.form', ['school' => $school, 'staffMember' => $school->staffMembers()->findOrFail($staff)]);
    }

    public function update(StaffRequest $request, School $school, int $staff): RedirectResponse
    {
        $school->staffMembers()->findOrFail($staff)->update($request->validated());

        return redirect()->route('staff.index', $school)->with('status', 'Staff member updated.');
    }

    public function destroy(Request $request, School $school, int $staff): RedirectResponse
    {
        $this->authorizeManagement($request);
        $school->staffMembers()->findOrFail($staff)->delete();

        return redirect()->route('staff.index', $school)->with('status', 'Staff member deleted.');
    }

    private function authorizeManagement(Request $request): void
    {
        abort_unless(in_array($request->attributes->get('school_role'), ['principal', 'deputy'], true), 403);
    }
}
