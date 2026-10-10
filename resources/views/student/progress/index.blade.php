@extends('layouts.student')
@section('title', $title.' | My Sports App')
@section('student-content')
<x-workspace-empty :school="$school" :title="$title" :description="$description" :message="$emptyMessage" :personal="true"></x-workspace-empty>
@endsection
