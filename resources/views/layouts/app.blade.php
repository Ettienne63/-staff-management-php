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
            $isPlatformAdmin = auth()->user()->isPlatformAdmin();
            $schoolRole = request()->attributes->get('school_role');
            $isFamilyPortal = request()->routeIs('family.*') || (request()->routeIs('invitations.*') && auth()->user()->usesFamilyPortal());
        @endphp
        <div class="app-shell">
            <aside class="sidebar" aria-label="Application navigation">
                <a class="sidebar-brand" href="{{ $isPlatformAdmin ? route('internal.schools.index') : ($isFamilyPortal ? route('family.dashboard') : ($schoolRole === 'student' ? route('schools.workspace', $currentSchool) : route('dashboard'))) }}">
                    <span class="sidebar-brand-mark" aria-hidden="true">S</span>
                    <span>SPORT<span class="sidebar-brand-light">SPACE</span><small>SCHOOL ATHLETICS</small></span>
                </a>
                <p class="sidebar-label">{{ $isPlatformAdmin ? 'PLATFORM' : ($isFamilyPortal ? 'FAMILY SPACE' : 'WORKSPACE') }}</p>
                <nav class="sidebar-nav" aria-label="Main navigation">
                    @if($isPlatformAdmin)
                        <a href="{{ route('internal.schools.index') }}" @if(request()->routeIs('internal.schools.index')) aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">&#9638;</span><span>Platform admin</span></a>
                        <a href="{{ route('internal.schools.create') }}" @if(request()->routeIs('internal.schools.create')) aria-current="page" @endif>Add school</a>
                        <a href="{{ route('internal.health') }}" @if(request()->routeIs('internal.health')) aria-current="page" @endif>Platform health</a>
                        <a href="{{ route('internal.activity') }}" @if(request()->routeIs('internal.activity')) aria-current="page" @endif>Admin activity</a>
                        <a href="{{ route('internal.announcements.index') }}" @if(request()->routeIs('internal.announcements.*')) aria-current="page" @endif>Announcements</a>
                    @elseif($isFamilyPortal)
                        <a href="{{ route('family.dashboard') }}" @if(request()->route('section') !== 'events') aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">&#8599;</span><span>Children&#8217;s progress</span></a>
                        <a href="{{ route('family.dashboard', ['section' => 'events']) }}" @if(request()->route('section') === 'events') aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">&#9719;</span><span>Family events</span></a>
                    @else
                        @if(auth()->user()->usesFamilyPortal())
                            <a href="{{ route('family.dashboard') }}"><span class="sidebar-icon" aria-hidden="true">&hearts;</span><span>Family space</span></a>
                        @endif
                        @if($schoolRole !== 'student')
                            <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">&#8962;</span><span>My schools</span></a>
                        @endif
                    @endif
                </nav>
                @if($currentSchool && ! $isPlatformAdmin)
                    <p class="sidebar-label sidebar-section-label">{{ $currentSchool->name }}</p>
                    @include($schoolRole === 'student' ? 'navigation.student' : 'navigation.school')
                @endif
                <div class="sidebar-bottom"><span class="sidebar-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span class="sidebar-user">{{ auth()->user()->name }}<small>{{ $schoolRole ? ucfirst($schoolRole) : 'Account' }}</small></span></div>
            </aside>
            <div class="app-main">
                <header class="topbar">
                    <div class="breadcrumb"><span>My Sports App</span>@if($isFamilyPortal)<span aria-hidden="true">/</span><strong>Family space</strong>@elseif($currentSchool)<span aria-hidden="true">/</span><strong>{{ $currentSchool->name }}</strong>@endif</div>
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
