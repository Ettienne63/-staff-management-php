@extends('layouts.school')
@section('title', $title.' | My Sports App')
@section('school-content')
<x-workspace-empty :school="$school" :title="$title" :description="$description" :message="$emptyMessage"></x-workspace-empty>
@endsection
