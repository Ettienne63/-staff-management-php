<nav class="sidebar-nav" aria-label="Student navigation">
    <a href="{{ route('schools.workspace', $currentSchool) }}" @if(request()->routeIs('schools.workspace')) aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">&#9635;</span><span>My dashboard</span></a>
    <a href="{{ route('schools.section', [$currentSchool, 'training']) }}" @if(request()->route('section') === 'training') aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">&#9719;</span><span>My training</span></a>
    <a href="{{ route('schools.section', [$currentSchool, 'progress']) }}" @if(request()->route('section') === 'progress') aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">&#8599;</span><span>My progress</span></a>
</nav>
