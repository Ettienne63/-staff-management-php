@extends('layouts.app')
@section('title', 'Your schools | My Sports App')
@section('content')
<div class="dashboard-grid selection-dashboard">
    <section class="dashboard-primary">
        <div class="dashboard-heading">
            <div><p class="eyebrow">Your school network</p><h1>Welcome back.</h1><p>Choose a school workspace to pick up where your team left off.</p></div>
        </div>
        <div class="section-heading"><h2>Your schools</h2><span>{{ $schools->count() }} {{ Str::plural('workspace', $schools->count()) }}</span></div>
        <div class="class-grid">
            @forelse($schools as $school)
                <form class="school-tile" action="{{ route('schools.select', $school) }}" method="POST">
                    @csrf
                    <span class="tile-topline"><span class="tile-mark" aria-hidden="true">{{ mb_substr($school->name, 0, 1) }}</span><span class="tile-arrow" aria-hidden="true">↗</span></span>
                    <span class="tile-name">{{ $school->name }}</span>
                    <span class="role-badge">{{ $school->pivot->role === 'deputy' ? 'Vice principal' : ($school->pivot->role === 'hr' ? 'HR' : ucfirst($school->pivot->role)) }}</span>
                    <button class="tile-open" type="submit">Open workspace <span aria-hidden="true">→</span></button>
                </form>
            @empty
                <section class="card empty-school"><p class="eyebrow">No workspace yet</p><h2>Access starts with your school.</h2><p>No school access has been granted to this account. Contact your school principal or HR team to get started.</p></section>
            @endforelse
        </div>
        <section class="dashboard-note card"><span class="note-icon" aria-hidden="true">✦</span><div><p class="eyebrow">Made for every season</p><h2>Small steps build strong athletes.</h2><p>Training and progress tools are organized by school, so each team can focus on its own journey.</p></div></section>
    </section>
    <aside class="dashboard-aside" aria-label="Daily overview">
        <section class="aside-panel today-panel">
            <div class="aside-heading"><div><p class="eyebrow">Your day</p><h2>Up next</h2></div><span class="panel-calendar" aria-hidden="true">◷</span></div>
            <div class="schedule-empty"><span class="empty-orbit" aria-hidden="true">◷</span><h3>Your schedule will live here.</h3><p>Choose a school to access its training and team information.</p></div>
        </section>
        <section class="aside-panel tip-panel"><p class="eyebrow">A space to grow</p><h2>Every student moves forward at their own pace.</h2><p>Keep participation, performance, and improvement in view as your school sports programme grows.</p></section>
    </aside>
</div>
@endsection
