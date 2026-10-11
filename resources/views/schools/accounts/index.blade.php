@extends('layouts.school')
@section('title', 'Account administration | My Sports App')
@section('school-content')
<div class="page-heading"><div><p class="eyebrow">{{ $school->name }}</p><h1>Account administration</h1><p>Invite a parent first. School leadership verifies child requests and grants separate student access.</p></div></div>
<section class="card form-panel">
    <h2>Invite a parent or guardian</h2><p>This invitation lets the parent set up a login and submit a child request. It grants no access to student records.</p>
    <form method="POST" action="{{ route('onboarding.guardians.invite', $school) }}">
        @csrf<label for="email">Parent's own email</label><input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255" required>
        <button class="primary" type="submit">Send parent invitation</button>
    </form>
</section>
<section class="card mt-6"><h2>Invitations</h2>
    <div class="table-scroll"><table><thead><tr><th>Email</th><th>Portal</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @forelse($invitations as $invitation)
            <tr><td>{{ $invitation->email }}</td><td>{{ $invitation->type === 'guardian' ? 'Parent' : 'Student' }}</td>
                <td>{{ $invitation->accepted_at ? 'Accepted' : ($invitation->revoked_at ? 'Revoked' : ($invitation->isUsable() ? 'Pending' : 'Expired')) }}</td>
                <td>@if($invitation->isUsable())<form method="POST" data-confirm="Revoke this unused invitation?" action="{{ route('onboarding.invitations.revoke', [$school, $invitation->id]) }}">@csrf @method('DELETE')<button class="danger" type="submit">Revoke invitation</button></form>@endif</td>
            </tr>
        @empty<tr><td colspan="4">No invitations yet.</td></tr>@endforelse
    </tbody></table></div>{{ $invitations->links() }}
</section>
@endsection
