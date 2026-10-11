@extends('layouts.school')
@section('title', 'Students | My Sports App')
@section('school-content')
<div class="page-heading"><div><p class="eyebrow">{{ $school->name }}</p><h1>Students</h1><p>Verified student records stay separate from parent and student login accounts.</p></div></div>
@if($canVerify)
    <h2>Child requests awaiting verification</h2>
    @forelse($registrations as $registration)
        <section class="card mb-6"><h3>{{ $registration->first_name }} {{ $registration->last_name }}</h3>
            <p>Born {{ $registration->date_of_birth->format('j M Y') }} &middot; Grade {{ $registration->grade }}<br>Requested by {{ $registration->guardian->name }} ({{ $registration->guardian->email }}) &middot; {{ str_replace('_', ' ', $registration->relationship) }}</p>
            <form method="POST" action="{{ route('onboarding.requests.review', [$school, $registration->id]) }}">
                @csrf
                <label for="student-{{ $registration->id }}">Student record</label><select id="student-{{ $registration->id }}" name="student_id"><option value="">Create a new student after verification</option>
                    @foreach($existingStudents as $student)<option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }} &middot; {{ $student->date_of_birth->format('j M Y') }} &middot; Grade {{ $student->grade }}</option>@endforeach
                </select>
                <label for="email-{{ $registration->id }}">Verified student's own email (optional)</label><input id="email-{{ $registration->id }}" name="email" type="email" maxlength="255" value="{{ $registration->student_email }}">
                <p>Confirm the student's email and guardian relationship before approving. Leave the email empty to create a child record without a login. Existing student records keep their school details.</p>
                <label for="note-{{ $registration->id }}">Review note (required when rejecting)</label><textarea id="note-{{ $registration->id }}" name="review_note" maxlength="1000"></textarea>
                <div class="actions"><button class="primary" name="decision" value="approve" type="submit">Verify and approve</button><button class="danger" name="decision" value="reject" type="submit">Reject request</button></div>
            </form>
        </section>
    @empty<p>No child requests awaiting verification.</p>@endforelse
    {{ $registrations->links() }}
@endif
<section class="card"><h2>Student roster</h2>
    @forelse($students as $student)
        <section class="mt-6"><h3>{{ $student->first_name }} {{ $student->last_name }}</h3><p>Grade {{ $student->grade }} &middot; Student login {{ $student->user_id ? 'activated' : 'not activated' }}</p>
            @if($canVerify)
                @foreach($student->guardians as $guardian)
                    <div class="actions"><p>Verified guardian: {{ $guardian->name }}</p><form method="POST" data-confirm="Remove this guardian's access to this child?" action="{{ route('onboarding.guardians.revoke', [$school, $student->id, $guardian->id]) }}">@csrf @method('DELETE')<button class="danger" type="submit">Remove guardian access</button></form></div>
                @endforeach
                @unless($student->user_id)
                    <form method="POST" action="{{ route('onboarding.students.invite', [$school, $student->id]) }}">@csrf
                        <label for="student-email-{{ $student->id }}">Student's own email</label><input id="student-email-{{ $student->id }}" name="email" type="email" maxlength="255" required>
                        <button class="primary" type="submit">Send or replace student invitation</button>
                    </form>
                @endunless
            @endif
        </section>
    @empty<p>No verified students yet.</p>@endforelse
    {{ $students->links() }}
</section>
@endsection
