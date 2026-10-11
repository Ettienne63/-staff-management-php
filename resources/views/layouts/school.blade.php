@extends('layouts.app')
@section('content')
@php($schoolRole = request()->attributes->get('school_role'))
<div class="workspace-banner">
    <div><p class="eyebrow">School workspace</p><p class="school-name">{{ $school->name }}</p></div>
    <a href="{{ route('dashboard') }}">All schools</a>
</div>
<nav class="workspace-nav" aria-label="School navigation">
    <a href="{{ route('schools.workspace', $school) }}" @if(request()->routeIs('schools.workspace')) aria-current="page" @endif>Overview</a>
    @if(in_array($schoolRole, \App\Models\User::StaffRoles, true))
        <a href="{{ route('staff.index', $school) }}" @if(request()->routeIs('staff.*')) aria-current="page" @endif>Staff</a>
        <a href="{{ route('schools.section', [$school, 'students']) }}" @if(request()->route('section') === 'students') aria-current="page" @endif>Students</a>
    @endif
    @if(in_array($schoolRole, \App\Models\User::ManagementRoles, true))
        <a href="{{ route('schools.section', [$school, 'accounts']) }}" @if(request()->route('section') === 'accounts') aria-current="page" @endif>Accounts</a>
    @endif
    <a href="{{ route('schools.section', [$school, 'training']) }}" @if(request()->route('section') === 'training') aria-current="page" @endif>{{ $schoolRole === 'student' ? 'My training' : 'Training' }}</a>
    <a href="{{ route('schools.section', [$school, 'progress']) }}" @if(request()->route('section') === 'progress') aria-current="page" @endif>{{ $schoolRole === 'student' ? 'My progress' : 'Progress' }}</a>
</nav>
@yield('school-content')
@endsection
