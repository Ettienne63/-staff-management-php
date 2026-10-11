@extends('layouts.app')
@section('title', 'Platform announcements | School Sports')
@section('content')
<p class="eyebrow">Internal platform administration</p><h1>Platform announcements</h1>
<section class="card form-panel">
    <h2>Publish a notice</h2><p>Notices appear on principal and deputy dashboards at every school. No email is sent.</p>
    <form method="POST" action="{{ route('internal.announcements.store') }}">
        @csrf
        <label for="title">Title</label><input id="title" name="title" value="{{ old('title') }}" maxlength="150" required>
        <label for="body">Message</label><textarea id="body" name="body" rows="5" maxlength="2000" required>{{ old('body') }}</textarea>
        <label for="expires_at">Hide from this date</label><input id="expires_at" name="expires_at" type="date" value="{{ old('expires_at') }}" min="{{ now()->addDay()->format('Y-m-d') }}" required>
        <p class="text-sm">Use platform notices only; do not include private school or family information.</p>
        <button type="submit" class="primary">Publish for school leadership</button>
    </form>
</section>
<section class="card mt-6"><h2>Notice history</h2>
    @forelse($announcements as $announcement)
        <article class="mt-4"><h3>{{ $announcement->title }}</h3><p class="whitespace-pre-line">{{ $announcement->body }}</p>
            <p class="text-sm">{{ $announcement->withdrawn_at ? 'Withdrawn' : (\Illuminate\Support\Carbon::parse($announcement->expires_at)->isPast() ? 'Expired' : 'Published') }} · Expires {{ $announcement->expires_at }}</p>
            @unless($announcement->withdrawn_at)<form method="POST" action="{{ route('internal.announcements.destroy', $announcement->id) }}">@csrf @method('DELETE')<button type="submit">Withdraw <span class="sr-only">{{ $announcement->title }}</span></button></form>@endunless
        </article>
    @empty<p>No announcements yet.</p>@endforelse
    {{ $announcements->links() }}
</section>
@endsection
