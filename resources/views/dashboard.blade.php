@extends('layouts.app')
@section('title', 'Dashboard | School Sports')
@section('content')
<div class="feature-banner">
<p class="eyebrow">Your team starts here</p>
<h1>Your schools</h1>
<p>Choose a school workspace.</p>
</div>
<div class="section-heading"><h2>School workspaces</h2><span>Authorized access</span></div>
<div class="school-grid">
    @forelse($schools as $school)
        <section class="card">
            <span class="module-number" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <h2>{{ $school->name }}</h2>
            <p><span class="role-badge">{{ $school->pivot->role === 'deputy' ? 'Vice principal' : ($school->pivot->role === 'hr' ? 'HR' : ucfirst($school->pivot->role)) }}</span></p>
            <form action="{{ route('schools.select', $school) }}" method="POST">
                @csrf<button class="primary" type="submit">Open school</button>
            </form>
        </section>
    @empty
        <section class="card"><p>No school access has been granted to this account.</p></section>
    @endforelse
</div>
@endsection
