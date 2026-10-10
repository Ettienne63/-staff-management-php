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
            <section class="card empty-state"><h2>Your child profiles will appear here.</h2><p>Guardian linking is not available yet. After the school verifies a relationship, each child will have a distinct profile with their own progress.</p><p>One guardian account may support several children, including children at different schools.</p></section>
        @endif
    </section>
    <aside class="dashboard-aside" aria-label="Family information"><section class="aside-panel tip-panel"><p class="eyebrow">Your child&#8217;s own record</p><h2>One family. Individual journeys.</h2><p>Each child&#8217;s training and progress will remain separate. Access will follow the relationship verified by their school.</p></section></aside>
</div>
@endsection
