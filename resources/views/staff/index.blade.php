@extends('layouts.school')
@section('title', 'Staff | School Sports')
@section('school-content')
<div class="page-heading">
    <div><a href="{{ route('dashboard') }}">All schools</a><h1>{{ $school->name }}</h1><p>Staff members</p></div>
    @if($canManage)<a class="button primary" href="{{ route('staff.create', $school) }}">Add staff</a>@endif
</div>
<section class="card">
    <div class="table-scroll">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Department</th><th>Position</th>@if($canManage)<th>Actions</th>@endif</tr></thead>
            <tbody>
            @forelse($staffMembers as $staff)
                <tr>
                    <td>{{ $staff->first_name }} {{ $staff->last_name }}</td>
                    <td>{{ $staff->email }}</td><td>{{ $staff->department }}</td><td>{{ $staff->position }}</td>
                    @if($canManage)
                        <td class="actions">
                            <a href="{{ route('staff.edit', [$school, $staff->id]) }}">Edit</a>
                            <form method="POST" data-confirm="Delete this staff member?" action="{{ route('staff.destroy', [$school, $staff->id]) }}">
                                @csrf @method('DELETE')<button class="danger" type="submit">Delete</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ $canManage ? 5 : 4 }}">No staff members yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $staffMembers->links() }}
</section>
@endsection
