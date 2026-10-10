@extends('layouts.school')
@section('title', $school->name.' | My Sports App')
@section('school-content')
<div class="dashboard-grid">
    <section class="dashboard-primary">
        <div class="dashboard-heading">
            <div><p class="eyebrow">{{ $school->name }} <span class="eyebrow-divider">/</span> {{ $roleLabel }}</p><h1>{{ $role === 'student' ? 'Your journey starts here.' : ($role === 'coach' ? 'Good to see you, coach.' : 'Let’s build a strong team.') }}</h1>
                <p>{{ $role === 'student' ? 'Your training and progress have a place to grow.' : ($role === 'coach' ? 'Your team’s training and development tools are gathered here.' : 'Everything you need to support sport and student development at your school.') }}</p></div>
        </div>
        <div class="section-heading"><h2>{{ $role === 'student' ? 'My sports space' : 'Your workspace' }}</h2><span>School sport · Grades 1–12</span></div>
        <div class="class-grid workspace-tiles">
            @if($staffCount !== null)
                <a class="workspace-tile tile-indigo" href="{{ route('staff.index', $school) }}"><span class="tile-topline"><span class="tile-mark" aria-hidden="true">♧</span><span class="tile-arrow" aria-hidden="true">↗</span></span><span class="tile-name">School staff</span><span class="tile-caption">{{ $staffCount }} {{ Str::plural('staff member', $staffCount) }}</span><span class="tile-open">Manage your team <span aria-hidden="true">→</span></span></a>
                <a class="workspace-tile tile-lilac" href="{{ route('schools.section', [$school, 'accounts']) }}"><span class="tile-topline"><span class="tile-mark" aria-hidden="true">＋</span><span class="tile-arrow" aria-hidden="true">↗</span></span><span class="tile-name">Accounts</span><span class="tile-caption">Access and school invitations</span><span class="tile-open">Account administration <span aria-hidden="true">→</span></span></a>
            @elseif($role === 'coach')
                <a class="workspace-tile tile-indigo" href="{{ route('schools.section', [$school, 'students']) }}"><span class="tile-topline"><span class="tile-mark" aria-hidden="true">♙</span><span class="tile-arrow" aria-hidden="true">↗</span></span><span class="tile-name">Students</span><span class="tile-caption">Your authorized school roster</span><span class="tile-open">View students <span aria-hidden="true">→</span></span></a>
            @endif
            <a class="workspace-tile tile-sky" href="{{ route('schools.section', [$school, 'training']) }}"><span class="tile-topline"><span class="tile-mark" aria-hidden="true">◷</span><span class="tile-arrow" aria-hidden="true">↗</span></span><span class="tile-name">{{ $role === 'student' ? 'My training' : 'Training' }}</span><span class="tile-caption">Activities, goals, and sessions</span><span class="tile-open">{{ $role === 'student' ? 'View my training' : 'Plan training' }} <span aria-hidden="true">→</span></span></a>
            <a class="workspace-tile tile-rose" href="{{ route('schools.section', [$school, 'progress']) }}"><span class="tile-topline"><span class="tile-mark" aria-hidden="true">↗</span><span class="tile-arrow" aria-hidden="true">↗</span></span><span class="tile-name">{{ $role === 'student' ? 'My progress' : 'Progress' }}</span><span class="tile-caption">Participation, performance, and growth</span><span class="tile-open">Explore progress <span aria-hidden="true">→</span></span></a>
        </div>
        <div class="section-heading lower-heading"><h2>Team updates</h2><span>School activity</span></div>
        <section class="update-panel card"><span class="update-icon" aria-hidden="true">✧</span><div><p class="eyebrow">A fresh start</p><h2>Your school space is ready.</h2><p>When training and student workflows are connected, your school updates will show up here.</p></div></section>
    </section>
    <aside class="dashboard-aside" aria-label="School day overview">
        <section class="aside-panel today-panel">
            <div class="aside-heading"><div><p class="eyebrow">{{ now()->timezone(config('app.timezone'))->format('l, j F') }}</p><h2>Today’s plan</h2></div><span class="panel-calendar" aria-hidden="true">◷</span></div>
            <div class="schedule-empty"><span class="empty-orbit" aria-hidden="true">◷</span><h3>No sessions scheduled yet.</h3><p>Planned training sessions will appear in your school schedule.</p><a class="aside-link" href="{{ route('schools.section', [$school, 'training']) }}">{{ $role === 'student' ? 'View training' : 'Go to training' }} <span aria-hidden="true">→</span></a></div>
        </section>
        <section class="aside-panel tip-panel"><p class="eyebrow">The big picture</p><h2>Progress is personal.</h2><p>Keep participation, performance, and improvement in view as students develop.</p><a class="aside-link" href="{{ route('schools.section', [$school, 'progress']) }}">{{ $role === 'student' ? 'My progress' : 'Explore progress' }} <span aria-hidden="true">→</span></a></section>
    </aside>
</div>
@endsection
