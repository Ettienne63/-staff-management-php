<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School Sports')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="sr-only focus:not-sr-only focus:block focus:p-4" href="#main-content">Skip to content</a>
    <header class="site-header">
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}">
            <img class="h-auto w-48" src="{{ asset('images/my-sports-app-logo-Photoroom.png') }}" alt="My Sports App" width="1408" height="768">
        </a>
        @auth
            <div class="account"><span>{{ auth()->user()->name }}</span>
                @if(auth()->user()->isPlatformAdmin())
                    <a href="{{ route('internal.schools.index') }}">Internal administration</a>
                @endif
                <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Sign out</button></form>
            </div>
        @endauth
    </header>
    <main id="main-content" class="container" tabindex="-1">
        @if(session('status'))<p class="notice" role="status">{{ session('status') }}</p>@endif
        @if($errors->any())
            <div class="errors" role="alert"><p>Please check the following:</p><ul>
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul></div>
        @endif
        @yield('content')
    </main>
</body>
</html>
