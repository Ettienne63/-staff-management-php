@extends('layouts.app')
@section('title', 'Accept invitation | My Sports App')
@section('content')
<section class="card form-panel">
    <p class="eyebrow">{{ $invitation->school->name }}</p><h1>Set up your {{ $invitation->type === 'guardian' ? 'parent' : 'student' }} portal</h1>
    <p>Invitation for {{ $invitation->email }}. Expires {{ $invitation->expires_at->format('j M Y') }}.</p>
    @if(auth()->check() && strtolower(auth()->user()->email) !== $invitation->email)
        <p>You are signed in with a different email. Sign out, then reopen this invitation.</p>
    @elseif($existingAccount && auth()->guest())
        <p>You already have an account. Sign in to accept without changing your password.</p><a class="button primary" href="{{ route('login') }}">Sign in to accept</a>
    @else
        <form method="POST" action="{{ route('invitations.accept', $token) }}">
            @csrf
            @guest
                <label for="name">Your name</label><input id="name" name="name" value="{{ old('name') }}" maxlength="255" autocomplete="name" required>
                <label for="password">Choose your password</label><input id="password" name="password" type="password" minlength="12" maxlength="72" autocomplete="new-password" required aria-describedby="password-help">
                <p id="password-help">At least 12 characters, including uppercase and lowercase letters and a number.</p>
                <label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            @endguest
            <button class="primary" type="submit">Accept invitation</button>
        </form>
    @endif
</section>
@endsection
