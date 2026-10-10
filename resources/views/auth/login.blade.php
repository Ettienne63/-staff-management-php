@extends('layouts.app')
@section('title', 'Sign in | My Sports App')
@section('content')
<div class="login-layout">
    <section class="login-story" aria-labelledby="login-intro">
        <div>
            <p class="eyebrow">School sport. Personal progress.</p>
            <h2 id="login-intro">A place for<br>every athlete.</h2>
            <p>Bring your school team together. Build good habits, support your students, and make every session count.</p>
        </div>
        <div>
            <div class="track-mark" aria-hidden="true"></div>
            <div class="login-points"><span>Track.</span><span>Connect.</span><span>Play.</span></div>
        </div>
    </section>
    <section class="login-card">
        <p class="eyebrow">Welcome to My Sports App</p>
        <h1>Sign in</h1>
        <p>Access your school workspace.</p>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <button class="primary" type="submit">Sign in <span aria-hidden="true">&rarr;</span></button>
        </form>
        <p class="mt-6 text-sm">Need school access? Contact your school's principal or HR team.</p>
    </section>
</div>
@endsection
