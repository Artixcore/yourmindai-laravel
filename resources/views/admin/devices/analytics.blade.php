@extends('layouts.app')
@section('title', 'Device Analytics')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Device Analytics</h1></div>
<div class="card"><div class="card-body">
<p>Active: {{ $activeDevices ?? 0 }}</p>
<p>Inactive: {{ $inactiveDevices ?? 0 }}</p>
</div></div>
@endsection
