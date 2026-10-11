@props(['school', 'audience', 'title', 'description', 'personal' => false, 'showSchedule' => true])
<div class="dashboard-grid">
    <section class="dashboard-primary">
        <div class="dashboard-heading"><div><p class="eyebrow">{{ $school->name }} / {{ $audience }} view</p><h1>{{ $title }}</h1><p>{{ $description }}</p></div></div>
        <div class="section-heading"><h2>{{ $personal ? 'My sports space' : 'Your workspace' }}</h2><span>School sport &middot; Grades 1&ndash;12</span></div>
        <div class="class-grid workspace-tiles">{{ $slot }}</div>
        <div class="section-heading lower-heading"><h2>{{ $personal ? 'My updates' : 'Workspace updates' }}</h2><span>Getting started</span></div>
        <section class="update-panel card"><span class="update-icon" aria-hidden="true">&#8599;</span><div><h2>Your space is ready.</h2><p>{{ $personal ? 'Your assigned training and personal progress will appear when those workflows are available.' : 'Student records, invitations, training, and results are being prepared for a later phase.' }}</p></div></section>
    </section>
    <aside class="dashboard-aside" aria-label="Workspace information">
        @if($showSchedule)
            <section class="aside-panel today-panel"><div class="aside-heading"><div><p class="eyebrow">Your day</p><h2>{{ $personal ? 'My schedule' : "Today's plan" }}</h2></div><span class="panel-calendar" aria-hidden="true">&#9719;</span></div>
                <div class="schedule-empty"><span class="empty-orbit" aria-hidden="true">&#8599;</span><h3>Training is not connected yet.</h3><p>{{ $personal ? 'Your assigned sessions will appear here.' : 'Planned sessions will appear here when scheduling is available.' }}</p><a class="aside-link" href="{{ route('schools.section', [$school, 'training']) }}">View training &rarr;</a></div>
            </section>
        @endif
        <section class="aside-panel tip-panel"><p class="eyebrow">{{ $audience }} space</p><h2>{{ $personal ? 'Progress is personal.' : 'Every role supports the team.' }}</h2><p>{{ $personal ? 'Follow your own participation, performance, and improvement.' : 'Your navigation follows your current school role and authorized access.' }}</p></section>
    </aside>
</div>
