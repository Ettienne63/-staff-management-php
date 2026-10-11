@extends('layouts.app')
@section('title', 'Edit public school details | School Sports')
@section('content')
<p class="eyebrow">Internal platform administration</p>
<h1>Edit public school details</h1>
<section class="card form-panel">
    <form method="POST" action="{{ route('internal.schools.update', $school) }}">
        @csrf
        @method('PUT')
        <label for="name">School name</label>
        <input id="name" name="name" value="{{ old('name', $school->name) }}" maxlength="255" required>
        @include('internal.public-school-fields')
        <div class="actions"><button type="submit" class="primary">Save public details</button><a href="{{ route('internal.schools.index') }}">Cancel</a></div>
    </form>
</section>
@endsection
