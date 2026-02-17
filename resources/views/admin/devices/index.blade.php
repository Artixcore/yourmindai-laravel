@extends('layouts.app')
@section('title', 'Device Management')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Device Management</h1></div>
<div class="card"><div class="card-body">
<p>Total devices: {{ $stats['total'] ?? 0 }}</p>
@if($devices->isEmpty())<p class="mb-0">No devices found.</p>
@else
<table class="table"><thead><tr><th>Device</th><th>Patient</th><th>Platform</th></tr></thead><tbody>
@foreach($devices as $d)<tr><td>{{ $d->device_name ?? '-' }}</td><td>{{ optional(optional($d->patientProfile)->user)->name ?? '-' }}</td><td>{{ $d->platform ?? '-' }}</td></tr>@endforeach
</tbody></table>{{ $devices->links() }}
@endif
</div></div>
@endsection
