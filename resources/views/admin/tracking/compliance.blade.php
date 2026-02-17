@extends('layouts.app')
@section('title', 'Compliance Report')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Compliance Report</h1><p class="text-muted mb-0">{{ $startDate }} to {{ $endDate }}</p></div>
<div class="card"><div class="card-body">
<p>Total patients: {{ $totalPatients }}</p>
<p>Mood: {{ $complianceMetrics['mood']['compliance_rate'] ?? 0 }}%</p>
<p>Sleep: {{ $complianceMetrics['sleep']['compliance_rate'] ?? 0 }}%</p>
<p>Exercise: {{ $complianceMetrics['exercise']['compliance_rate'] ?? 0 }}%</p>
</div></div>
@endsection
