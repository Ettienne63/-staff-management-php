@extends('layouts.school')
@section('title', $title.' | My Sports App')
@section('school-content')
<x-workspace-empty :school="$school" :title="$title" :description="$description" :message="$emptyMessage"><p>Principal, deputy, and HR will manage day-to-day access. Deputy appointments and ownership transfer are reserved for the principal.</p></x-workspace-empty>
@endsection
