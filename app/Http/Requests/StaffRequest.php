<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->attributes->get('school_role'), ['principal', 'deputy'], true);
    }

    public function rules(): array
    {
        $school = $this->route('school');
        $staff = $this->route('staff');

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('staff_members', 'email')
                ->where('school_id', $school->id)->ignore($staff ? $school->staffMembers()->findOrFail($staff)->id : null)],
            'department' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:150'],
        ];
    }
}
