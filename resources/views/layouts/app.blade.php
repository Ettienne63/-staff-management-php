<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Sports App')</title>
    <meta name="theme-color" content="#082b49">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="sr-only focus:not-sr-only focus:block focus:p-4" href="#main-content">Skip to content</a>
    <header class="site-header">
        <div class="masthead">
        <a class="brand" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
            <img class="brand-logo" src="{{ asset('images/my-sports-app-logo-Photoroom.png') }}" alt="My Sports App" width="1408" height="768">
            <span class="brand-tagline">Track. Connect. Play.</span>
        </a>
        @auth
            <div class="account"><span class="account-name"><small>Signed in</small>{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">@csrf<button class="header-signout" type="submit">Sign out</button></form>
            </div>
        @endauth
        </div>
    </header>
    @auth
        <nav class="global-nav" aria-label="Main navigation"><div class="global-nav-inner">
            <a href="{{ route('dashboard') }}" @if(!request()->routeIs('internal.*')) aria-current="page" @endif>School workspace</a>
            @if(auth()->user()->isPlatformAdmin())
                <a href="{{ route('internal.schools.index') }}" @if(request()->routeIs('internal.*')) aria-current="page" @endif>Internal administration</a>
            @endif
        </div></nav>
    @endauth
    <main id="main-content" class="container" tabindex="-1">
        @if(session('status'))<p class="notice" role="status">{{ session('status') }}</p>@endif
        @if($errors->any())
            <div class="errors" role="alert"><p>Please check the following:</p><ul>
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul></div>
        @endif
        @yield('content')
    </main>
    <footer class="site-footer"><span>MY SPORTS APP</span><span>School sport &middot; Grades 1&ndash;12</span></footer>
</body>
</html>
