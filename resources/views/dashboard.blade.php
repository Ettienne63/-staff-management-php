@extends('layouts.app')
@section('title', 'Dashboard | School Sports')
@section('content')
<p class="eyebrow">School workspace</p>
<h1>Your schools</h1>
<p>Choose a school workspace.</p>
<div class="school-grid">
    @forelse($schools as $school)
        <section class="card">
            <h2>{{ $school->name }}</h2>
            <p>{{ ucfirst($school->pivot->role) }}</p>
            <form action="{{ route('schools.select', $school) }}" method="POST">
                @csrf<button class="primary" type="submit">Open school</button>
            </form>
        </section>
    @empty
        <section class="card"><p>No school access has been granted to this account.</p></section>
    @endforelse
</div>
@endsection
