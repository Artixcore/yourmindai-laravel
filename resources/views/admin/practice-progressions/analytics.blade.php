@extends('layouts.app')
@section('title', 'Progression Analytics')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Progression Analytics</h1><p class="text-muted mb-0">{{ $startDate }} to {{ $endDate }}</p></div>
<div class="card"><div class="card-body">
<p>Total: {{ $totalProgressions ?? 0 }}</p>
<p>Avg progress: {{ $avgProgress ?? 0 }}%</p>
<p>Completion rate: {{ $completionRate ?? 0 }}%</p>
</div></div>
@endsection
