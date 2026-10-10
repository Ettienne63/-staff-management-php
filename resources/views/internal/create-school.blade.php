@extends('layouts.app')
@section('title', 'Add school | School Sports')
@section('content')
<p class="eyebrow">Internal platform administration</p>
<h1>Add a school</h1>
<section class="card form-panel">
    <p>Create a separate school workspace. Assign an existing account as its first principal.</p>
    <form method="POST" action="{{ route('internal.schools.store') }}">
        @csrf
        <label for="name">School name</label>
        <input id="name" name="name" value="{{ old('name') }}" maxlength="255" required>
        <label for="principal_email">Principal account email</label>
        <input id="principal_email" name="principal_email" type="email" value="{{ old('principal_email') }}" maxlength="255" aria-describedby="principal-help" required>
        <p id="principal-help" class="text-sm">This must be an existing login account. Invitation signup is planned for a later phase.</p>
        <div class="actions"><button type="submit" class="primary">Create school</button><a href="{{ route('internal.schools.index') }}">Cancel</a></div>
    </form>
</section>
@endsection
