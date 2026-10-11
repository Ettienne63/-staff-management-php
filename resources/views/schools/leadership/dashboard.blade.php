@extends('layouts.school')
@section('title', $roleLabel.' workspace | My Sports App')
@section('school-content')
<x-workspace-dashboard :school="$school" :audience="$roleLabel" title="Let’s build a strong team." description="Coordinate your school team, student development, and account access.">
    <x-workspace-tile :href="route('staff.index', $school)" title="School staff" :caption="$staffCount.' '.Str::plural('staff member', $staffCount)" action="Manage staff" icon="♧" />
    <x-workspace-tile :href="route('schools.section', [$school, 'accounts'])" title="Accounts" caption="School invitations and access" action="Manage access" tone="lilac" icon="+" />
    <x-workspace-tile :href="route('schools.section', [$school, 'students'])" title="Student roster" caption="School student records" action="View students" tone="indigo" icon="♙" />
    <x-workspace-tile :href="route('schools.section', [$school, 'training'])" title="Training" caption="Activities, goals, and sessions" action="Explore training" tone="sky" icon="◷" />
    <x-workspace-tile :href="route('schools.section', [$school, 'progress'])" title="Student progress" caption="Participation, performance, and growth" action="Explore progress" tone="rose" icon="↗" />
</x-workspace-dashboard>
@if($role === 'principal')
    <section class="card mt-6"><h2>School ownership</h2><p>Deputy appointments and ownership transfer are reserved for the principal. These controls will arrive with account administration.</p></section>
@endif
@endsection
