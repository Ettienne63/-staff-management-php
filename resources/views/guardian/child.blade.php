@extends('layouts.guardian')
@section('title', $child->first_name.' | Family progress')
@section('guardian-content')
<section class="card">
    <a href="{{ route('family.dashboard') }}">Back to children's progress</a>
    <p class="eyebrow mt-6">Verified child profile</p><h1>{{ $child->first_name }} {{ $child->last_name }}</h1>
    <p>{{ $child->school->name }} &middot; Grade {{ $child->grade }}</p>
    <p>Student portal: {{ $child->user_id ? 'Account activated' : 'Not activated yet' }}.</p>
    @if(! $child->user_id)
        <h2>Activate student access</h2>
        <p>Send an invitation to your child's own email. Their account will belong to {{ $child->school->name }}.</p>
        <form method="POST" action="{{ route('family.children.invite', $child) }}">
            @csrf
            <label for="student_email">Student email</label>
            <input id="student_email" name="email" type="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" required>
            <button class="primary" type="submit">Send student invitation</button>
        </form>
    @endif
    <h2>Training and progress</h2><p>No assessments or sports results are recorded yet. Your child's progress will appear here when those workflows are available.</p>
</section>
@endsection
