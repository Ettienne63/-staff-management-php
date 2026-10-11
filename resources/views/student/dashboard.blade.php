@extends('layouts.student')
@section('title', $roleLabel.' workspace | My Sports App')
@section('student-content')
@if($student)
    <section class="card"><p class="eyebrow">My student profile</p><h1>{{ $student->first_name }} {{ $student->last_name }}</h1><p>{{ $school->name }} &middot; Grade {{ $student->grade }}</p></section>
@endif
<x-workspace-dashboard :school="$school" :audience="$roleLabel" title="Your journey starts here." description="Your training, goals, and personal progress in one place." :personal="true">
    <x-workspace-tile :href="route('schools.section', [$school, 'training'])" title="My training" caption="My assigned activities and sessions" action="View my training" tone="sky" icon="◷" />
    <x-workspace-tile :href="route('schools.section', [$school, 'progress'])" title="My progress" caption="My participation, performance, and improvement" action="View my progress" tone="rose" icon="↗" />
</x-workspace-dashboard>
@endsection
