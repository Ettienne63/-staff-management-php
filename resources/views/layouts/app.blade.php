<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Sports App')</title>
    <meta name="theme-color" content="#0b315f">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="sr-only focus:not-sr-only focus:block focus:p-4" href="#main-content">Skip to content</a>
    @auth
        @php
            $currentSchool = request()->route('school');
            $schoolRole = request()->attributes->get('school_role');
        @endphp
        <div class="app-shell">
            <aside class="sidebar" aria-label="Application navigation">
                <a class="sidebar-brand" href="{{ route('dashboard') }}">
                    <span class="sidebar-brand-mark" aria-hidden="true">S</span>
                    <span>SPORT<span class="sidebar-brand-light">SPACE</span><small>SCHOOL ATHLETICS</small></span>
                </a>
                <p class="sidebar-label">WORKSPACE</p>
                <nav class="sidebar-nav" aria-label="Main navigation">
                    @unless(auth()->user()->schools()->wherePivotIn('role', \App\Models\User::LeadershipRoles)->exists())
                        <a href="{{ route('family.dashboard') }}" @if(request()->routeIs('family.*')) aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">&hearts;</span><span>Family space</span></a>
                    @endunless
                    <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">⌂</span><span>My schools</span></a>
                    @if(auth()->user()->isPlatformAdmin())
                        <a href="{{ route('internal.schools.index') }}" @if(request()->routeIs('internal.*')) aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">▦</span><span>Platform admin</span></a>
                    @endif
                </nav>
                @if($currentSchool)
                    <p class="sidebar-label sidebar-section-label">{{ $currentSchool->name }}</p>
                    @include($schoolRole === 'student' ? 'navigation.student' : 'navigation.school')
                @endif
                <div class="sidebar-bottom"><span class="sidebar-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span class="sidebar-user">{{ auth()->user()->name }}<small>{{ $schoolRole ? ucfirst($schoolRole) : 'Account' }}</small></span></div>
            </aside>
            <div class="app-main">
                <header class="topbar">
                    <div class="breadcrumb"><span>My Sports App</span>@if($currentSchool)<span aria-hidden="true">/</span><strong>{{ $currentSchool->name }}</strong>@endif</div>
                    <div class="topbar-actions"><span class="today-date">{{ now()->timezone(config('app.timezone'))->format('D, j M Y') }}</span><form action="{{ route('logout') }}" method="POST">@csrf<button class="header-signout" type="submit">Sign out <span aria-hidden="true">↗</span></button></form></div>
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
                <footer class="site-footer"><span>MY SPORTS APP</span><span>School sport &middot; Grades 1&ndash;12</span></footer>
            </div>
        </div>
    @else
        <header class="site-header"><div class="masthead"><a class="brand" href="{{ route('login') }}"><img class="brand-logo" src="{{ asset('images/my-sports-app-logo-Photoroom.png') }}" alt="My Sports App" width="1408" height="768"><span class="brand-tagline">Track. Connect. Play.</span></a></div></header>
        <main id="main-content" class="container public-container" tabindex="-1">
            @if(session('status'))<p class="notice" role="status">{{ session('status') }}</p>@endif
            @if($errors->any())<div class="errors" role="alert"><p>Please check the following:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
        <footer class="site-footer"><span>MY SPORTS APP</span><span>School sport &middot; Grades 1&ndash;12</span></footer>
    @endauth
</body>
</html>
