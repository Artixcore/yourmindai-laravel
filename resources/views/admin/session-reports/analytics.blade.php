@extends('layouts.app')
@section('title', 'Session Report Analytics')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Session Report Analytics</h1></div>
<div class="card"><div class="card-body">
<p>Total: {{ $totalReports ?? 0 }}</p>
<p>Finalized: {{ $finalizedReports ?? 0 }}</p>
<p>Rate: {{ $finalizationRate ?? 0 }}%</p>
</div></div>
@endsection
