@extends('layouts.school')
@section('title', $title.' | My Sports App')
@section('school-content')
<x-workspace-empty :school="$school" :title="$title" :description="$description" :message="$emptyMessage">@if($role === 'coach')<p>You will be able to plan sessions and edit training and results for your authorized students.</p>@endif</x-workspace-empty>
@endsection
