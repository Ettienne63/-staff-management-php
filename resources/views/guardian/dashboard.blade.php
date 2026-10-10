@extends('layouts.guardian')
@section('title', ($section === 'events' ? 'Family events' : 'Children’s progress').' | My Sports App')
@section('guardian-content')
<div class="dashboard-grid">
    <section class="dashboard-primary">
        <div class="dashboard-heading">
            <div><p class="eyebrow">Family space</p>
                @if($section === 'events')
                    <h1>Family events and updates.</h1><p>Keep up with school sports events, fixtures, and family updates.</p>
                @else
                    <h1>Your children&#8217;s progress.</h1><p>Follow each child&#8217;s sports journey.</p>
                @endif
            </div>
        </div>
        @if($section === 'events')
            <section class="card empty-state"><h2>Keep family sports events in one place.</h2><p>School events, fixtures, and family updates will appear here when those features are available.</p></section>
        @else
            <div class="section-heading"><h2>Children&#8217;s progress</h2><span>Verified school relationships</span></div>
            <div class="class-grid">
                @forelse($children as $child)
                    <x-workspace-tile :href="route('family.children.show', $child)" :title="$child->first_name.' '.$child->last_name" :caption="$child->school->name.' · Grade '.$child->grade" action="View child progress" tone="sky" icon="↗" />
                @empty
                    <section class="card empty-state empty-school"><h2>No verified children yet.</h2><p>The school must verify your child and guardian relationship before their profile becomes available.</p></section>
                @endforelse
            </div>
            <section class="card form-panel mt-6">
                <h2>Register or link your child</h2><p>Submit one request for each child. School verification is required before you can view a student profile.</p>
                @if($schools->isNotEmpty())
                    <form method="POST" action="{{ route('family.requests.store') }}">
                        @csrf
                        <label for="school_id">Child's school</label><select id="school_id" name="school_id" required>
                            @foreach($schools as $school)<option value="{{ $school->id }}" @selected(old('school_id') == $school->id)>{{ $school->name }}</option>@endforeach
                        </select>
                        <label for="first_name">Child's first name</label><input id="first_name" name="first_name" value="{{ old('first_name') }}" maxlength="100" autocomplete="off" required>
                        <label for="last_name">Child's last name</label><input id="last_name" name="last_name" value="{{ old('last_name') }}" maxlength="100" autocomplete="off" required>
                        <label for="date_of_birth">Date of birth</label><input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" max="{{ today()->subDay()->format('Y-m-d') }}" required>
                        <label for="grade">Grade</label><input id="grade" name="grade" type="number" min="1" max="12" value="{{ old('grade') }}" required>
                        <label for="relationship">Your relationship</label><select id="relationship" name="relationship" required><option value="parent" @selected(old('relationship') === 'parent')>Parent</option><option value="legal_guardian" @selected(old('relationship') === 'legal_guardian')>Legal guardian</option></select>
                        <label for="student_email">Student's own email (optional)</label><input id="student_email" name="student_email" type="email" maxlength="255" value="{{ old('student_email') }}" aria-describedby="student-email-help">
                        <p id="student-email-help">Use an email owned by your child for a separate student login. Leave it empty if your child does not have one.</p>
                        <button class="primary" type="submit">Request school verification</button>
                    </form>
                @else
                    <p>Ask your school for a parent invitation before submitting a child request.</p>
                @endif
            </section>
            <section class="card mt-6"><h2>Your requests</h2>
                @forelse($registrations as $registration)
                    <p><strong>{{ $registration->first_name }} {{ $registration->last_name }}</strong> &middot; {{ $registration->school->name }} &middot; {{ ucfirst($registration->status) }}</p>
                    @if($registration->review_note)<p>{{ $registration->review_note }}</p>@endif
                @empty<p>No child requests submitted yet.</p>@endforelse
                {{ $registrations->links() }}
            </section>
        @endif
    </section>
    <aside class="dashboard-aside" aria-label="Family information"><section class="aside-panel tip-panel"><p class="eyebrow">Your child&#8217;s own record</p><h2>One family. Individual journeys.</h2><p>Each child&#8217;s training and progress will remain separate. Access will follow the relationship verified by their school.</p></section></aside>
</div>
@endsection
