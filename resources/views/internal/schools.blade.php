@extends('layouts.app')
@section('title', 'Internal schools | School Sports')
@section('content')
<div class="page-heading">
    <div><p class="eyebrow">Internal platform administration</p><h1>Schools</h1><p>Create school workspaces and assign their first principal.</p></div>
    <a class="button primary" href="{{ route('internal.schools.create') }}">Add school</a>
</div>
<nav class="workspace-nav" aria-label="Platform navigation">
    <a href="{{ route('internal.schools.index') }}" aria-current="page">Schools</a>
    <a href="{{ route('dashboard') }}">My school workspaces</a>
</nav>
<section class="card">
    <h2>School directory</h2>
    <p class="text-sm text-slate-600">Platform administration does not grant access to a school's staff or student records.</p>
    <div class="table-scroll"><table>
        <thead><tr><th scope="col">School</th><th scope="col">Created</th></tr></thead>
        <tbody>
        @forelse($schools as $school)
            <tr><td>{{ $school->name }}</td><td>{{ $school->created_at->format('d M Y') }}</td></tr>
        @empty
            <tr><td colspan="2">No schools yet. Add a school to get started.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $schools->links() }}
</section>
@endsection
