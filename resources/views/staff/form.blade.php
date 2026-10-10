@extends('layouts.school')
@section('title', ($staffMember ? 'Edit staff' : 'Add staff').' | School Sports')
@section('school-content')
<section class="card">
    <a href="{{ route('staff.index', $school) }}">Back to staff</a>
    <h1>{{ $staffMember ? 'Edit staff member' : 'Add staff member' }}</h1>
    <p>{{ $school->name }}</p>
    <form method="POST" action="{{ $staffMember ? route('staff.update', [$school, $staffMember->id]) : route('staff.store', $school) }}">
        @csrf
        @if($staffMember)@method('PUT')@endif
        @foreach(['first_name' => 'First name', 'last_name' => 'Last name', 'email' => 'Email', 'department' => 'Department', 'position' => 'Position'] as $field => $label)
            <label for="{{ $field }}">{{ $label }}</label>
            <input id="{{ $field }}" name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}"
                maxlength="{{ in_array($field, ['first_name', 'last_name']) ? 100 : ($field === 'email' ? 255 : 150) }}"
                value="{{ old($field, $staffMember?->$field) }}" required>
        @endforeach
        <div class="actions"><button class="primary" type="submit">Save staff member</button>
            <a href="{{ route('staff.index', $school) }}">Cancel</a></div>
    </form>
</section>
@endsection
