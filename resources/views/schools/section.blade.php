@extends('layouts.school')
@section('title', $title.' | School Sports')
@section('school-content')
<h1>{{ $title }}</h1>
<p>{{ $description }}</p>
<section class="card max-w-3xl">
    <p class="eyebrow">Coming in a later phase</p>
    <h2>{{ $section === 'accounts' ? 'Access workflows are planned' : 'Ready for the next step' }}</h2>
    <p>{{ $emptyMessage }}</p>
    @if($section === 'accounts')
        <p>Principal, vice, and HR will manage access. Vice appointments and school ownership changes are reserved for the principal.</p>
    @elseif($section === 'training' && $role === 'student')
        <p>You will be able to mark assigned exercises complete. Sharing an evidence link is planned for a future update.</p>
    @elseif($section === 'training' && $role === 'coach')
        <p>You will be able to plan sessions and edit training and results for students you are authorized to see.</p>
    @endif
    <a class="button" href="{{ route('schools.workspace', $school) }}">Back to overview</a>
</section>
@endsection
