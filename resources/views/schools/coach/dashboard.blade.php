@extends('layouts.school')
@section('title', $roleLabel.' workspace | My Sports App')
@section('school-content')
<x-workspace-dashboard :school="$school" :audience="$roleLabel" title="Good to see you, coach." description="Focus on your authorized students, their training, and their development.">
    <x-workspace-tile :href="route('schools.section', [$school, 'students'])" title="My student roster" caption="Authorized school students" action="View students" tone="indigo" icon="♙" />
    <x-workspace-tile :href="route('schools.section', [$school, 'training'])" title="Training planner" caption="Activities, goals, and sessions" action="Explore training" tone="sky" icon="◷" />
    <x-workspace-tile :href="route('schools.section', [$school, 'progress'])" title="Student progress" caption="Participation, performance, and growth" action="Explore progress" tone="rose" icon="↗" />
</x-workspace-dashboard>
@endsection
