@extends('layouts.guardian')
@section('title', $child->first_name.' | Family progress')
@section('guardian-content')
<section class="card">
    <a href="{{ route('family.dashboard') }}">Back to children's progress</a>
    <p class="eyebrow mt-6">Verified child profile</p><h1>{{ $child->first_name }} {{ $child->last_name }}</h1>
    <p>{{ $child->school->name }} &middot; Grade {{ $child->grade }}</p>
    <p>Student portal: {{ $child->user_id ? 'Account activated' : 'Not activated yet' }}.</p>
    <h2>Training and progress</h2><p>No assessments or sports results are recorded yet. Your child's progress will appear here when those workflows are available.</p>
</section>
@endsection
