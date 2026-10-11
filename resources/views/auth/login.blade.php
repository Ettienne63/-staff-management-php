@extends('layouts.app')
@section('title', 'Sign in | School Sports')
@section('content')
<section class="card login-card">
    <h1>Sign in</h1>
    <p>Access your school workspace.</p>
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button class="primary" type="submit">Sign in</button>
    </form>
</section>
@endsection
