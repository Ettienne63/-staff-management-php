@props(['school', 'title', 'description', 'message', 'personal' => false])
<div class="page-heading"><div><p class="eyebrow">{{ $personal ? 'My sports journey' : 'School workspace' }}</p><h1>{{ $title }}</h1><p>{{ $description }}</p></div></div>
<section class="card empty-state max-w-3xl"><p class="eyebrow">Coming in a later phase</p><h2>Ready for the next step</h2><p>{{ $message }}</p>{{ $slot }}<a class="button" href="{{ route('schools.workspace', $school) }}">{{ $personal ? 'Back to my dashboard' : 'Back to overview' }}</a></section>
