@extends('layouts.app')
@section('title', 'Internal schools | School Sports')
@section('content')
<div class="page-heading feature-banner">
    <div><p class="eyebrow">Internal platform administration</p><h1>Schools</h1><p>Create school workspaces and assign their first principal.</p></div>
    <a class="button primary" href="{{ route('internal.schools.create') }}">Add school</a>
</div>
<nav class="workspace-nav" aria-label="Platform navigation">
    <a href="{{ route('internal.schools.index') }}" aria-current="page">Schools</a>
</nav>
<section class="card">
    <h2>School directory</h2>
    <p class="text-sm">Platform administration does not grant access to a school's staff or student records.</p>
    <div class="table-scroll"><table>
        <thead><tr><th scope="col">School</th><th scope="col">Setup</th><th scope="col">Public contact</th><th scope="col">Created</th><th scope="col">Actions</th></tr></thead>
        <tbody>
        @forelse($schools as $school)
            <tr>
                <td>{{ $school->name }}</td>
                <td>{{ $school->leadership_assigned ? 'Leadership assigned' : 'Needs leadership assignment' }}<br><span class="text-sm">{{ $school->public_email || $school->public_phone || $school->website ? 'Public contact provided' : 'Public contact not provided' }}</span></td>
                <td>{{ $school->public_email ?? '—' }}<br>{{ $school->public_phone }}<br>@if($school->website)<a href="{{ $school->website }}" rel="noopener noreferrer">School website</a>@endif</td>
                <td>{{ $school->created_at->format('d M Y') }}</td>
                <td><a href="{{ route('internal.schools.edit', $school) }}" aria-label="Edit public details for {{ $school->name }}">Edit public details</a></td>
            </tr>
        @empty
            <tr><td colspan="5">No schools yet. Add a school to get started.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $schools->links() }}
</section>
@endsection
