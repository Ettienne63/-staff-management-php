@extends('layouts.student')
@section('title', $title.' | My Sports App')
@section('student-content')
<x-workspace-empty :school="$school" :title="$title" :description="$description" :message="$emptyMessage" :personal="true"><p>You will be able to mark assigned exercises complete. Evidence links are planned for a future update.</p></x-workspace-empty>
@endsection
