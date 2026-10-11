@extends('layouts.app')
@section('title', 'Platform health | School Sports')
@section('content')
<p class="eyebrow">Internal platform administration</p><h1>Platform health</h1>
<section class="card">
    <p>Current checks as of {{ now()->format('d M Y H:i:s') }}. Refresh for another check. Historical uptime monitoring is not connected.</p>
    <dl>
        <dt>Application</dt><dd>Responding to this request</dd>
        <dt>Database</dt><dd>{{ $databaseAvailable ? 'Available' : 'Unavailable' }}</dd>
        <dt>Application storage</dt><dd>{{ $storageWritable ? 'Writable' : 'Not writable' }}</dd>
        <dt>Free space on application storage volume</dt><dd>{{ $freeGigabytes === null ? 'Unavailable' : $freeGigabytes.' GB' }}</dd>
        <dt>Last backup report</dt><dd>@if($backup){{ ucfirst($backup->result) }} reported at {{ $backup->created_at }}@else Not reported @endif</dd>
    </dl>
    <p class="text-sm mt-4">Backup status reflects the last result reported by the backup process. It does not verify backup contents or restore readiness.</p>
</section>
@endsection
