@extends('layouts.school')
@section('title', $roleLabel.' workspace | School Sports')
@section('school-content')
<div class="page-heading">
    <div><p class="eyebrow">{{ $roleLabel }} view</p><h1>{{ $role === 'student' ? 'Your sports journey' : ($role === 'coach' ? 'Coach workspace' : 'School administration') }}</h1>
    <p>{{ $role === 'student' ? 'Your training, goals, and progress will live here.' : ($role === 'coach' ? 'Plan training and follow the students at your school.' : 'Manage your school team and coordinate access.') }}</p></div>
</div>
<div class="school-grid">
    @if($staffCount !== null)
        <section class="card"><p class="eyebrow">School team</p><h2>{{ $staffCount }} staff records</h2><p>Maintain contact details, departments, and positions.</p><a class="button primary" href="{{ route('staff.index', $school) }}">Manage staff</a></section>
        <section class="card"><p class="eyebrow">People and access</p><h2>Account administration</h2><p>HR coordinates invitations and access with school leadership.</p><a href="{{ route('schools.section', [$school, 'accounts']) }}">View account information</a></section>
    @elseif($role === 'coach')
        <section class="card"><p class="eyebrow">Your students</p><h2>Student roster</h2><p>School student profiles will appear here when the roster is connected.</p><a href="{{ route('schools.section', [$school, 'students']) }}">View students</a></section>
    @endif
    <section class="card"><p class="eyebrow">Activities and goals</p><h2>{{ $role === 'student' ? 'My training' : 'Training planner' }}</h2><p>No training is connected yet. Scheduled sessions and goals will appear here.</p><a href="{{ route('schools.section', [$school, 'training']) }}">View training</a></section>
    <section class="card"><p class="eyebrow">Personal improvement</p><h2>{{ $role === 'student' ? 'My progress' : 'Student progress' }}</h2><p>No assessment results yet. Follow participation, performance, and improvement as results are recorded.</p><a href="{{ route('schools.section', [$school, 'progress']) }}">View progress</a></section>
</div>
@if($role === 'principal')
    <section class="card mt-6"><h2>School ownership</h2><p>As principal, you are responsible for vice appointments and ownership handover. These controls will be added with the account administration workflow.</p></section>
@endif
@endsection
