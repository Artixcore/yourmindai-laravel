@extends('layouts.app')
@section('title', 'Device Details')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Device Details</h1></div>
<div class="card"><div class="card-body">
<p>Device: {{ $device->device_name ?? '-' }}</p>
<p>Patient: {{ optional(optional($device->patientProfile)->user)->name ?? optional($device->patient)->name ?? '-' }}</p>
<p>Type: {{ ucfirst($device->device_type ?? '-') }}</p>
<p>OS: {{ $device->os_type ?? '-' }} {{ $device->os_version ?? '' }}</p>
</div></div>
@endsection
