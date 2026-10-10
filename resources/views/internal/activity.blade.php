@extends('layouts.app')
@section('title', 'Admin activity | School Sports')
@section('content')
<p class="eyebrow">Internal platform administration</p><h1>Admin activity</h1>
<section class="card">
    <p>School registration, public detail corrections, and platform announcements. Private school activity is excluded.</p>
    <div class="table-scroll"><table>
        <thead><tr><th scope="col">When</th><th scope="col">Action</th><th scope="col">Admin account ID</th><th scope="col">Public details</th></tr></thead>
        <tbody>@forelse($activities as $activity)
            <tr><td>{{ $activity->created_at }}</td><td>{{ $activity->action }}</td><td>{{ $activity->actor_id ?? 'Removed account' }}</td><td><pre class="whitespace-pre-wrap break-words">{{ json_encode(json_decode($activity->details), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></td></tr>
        @empty<tr><td colspan="4">No admin activity recorded yet. Earlier changes were not tracked.</td></tr>@endforelse</tbody>
    </table></div>
    {{ $activities->links() }}
</section>
@endsection
