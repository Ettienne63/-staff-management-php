@extends('layouts.guardian')
@section('title', 'Family space | My Sports App')
@section('guardian-content')
<div class="dashboard-grid">
    <section class="dashboard-primary"><div class="dashboard-heading"><div><p class="eyebrow">Parents and guardians</p><h1>Your family&#8217;s sports journey.</h1><p>A dedicated space for each child you are authorized to support.</p></div></div>
        <div class="section-heading"><h2>My children</h2><span>Verified school relationships</span></div>
        <section class="card empty-state"><h2>Family links are coming next.</h2><p>Guardian linking is not available yet. After the school verifies a relationship, each child will have a distinct profile here.</p><p>One guardian account may support several children, including children at different schools.</p></section>
    </section>
    <aside class="dashboard-aside" aria-label="Family information"><section class="aside-panel tip-panel"><p class="eyebrow">Your child&#8217;s own record</p><h2>One family. Individual journeys.</h2><p>Each child&#8217;s training and progress will remain separate. Access will follow the relationship verified by their school.</p></section></aside>
</div>
@endsection
