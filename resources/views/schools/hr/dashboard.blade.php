@extends('layouts.school')
@section('title', $roleLabel.' workspace | My Sports App')
@section('school-content')
<x-workspace-dashboard :school="$school" :audience="$roleLabel" title="Keep your school connected." description="Manage staff records and coordinate account access with school leadership." :show-schedule="false">
    <x-workspace-tile :href="route('staff.index', $school)" title="School staff" :caption="$staffCount.' '.Str::plural('staff member', $staffCount)" action="Manage staff" icon="♧" />
    <x-workspace-tile :href="route('schools.section', [$school, 'accounts'])" title="Account administration" caption="Invitations and school access" action="Explore accounts" tone="lilac" icon="+" />
</x-workspace-dashboard>
@endsection
