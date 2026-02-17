@extends('layouts.app')
@section('title', 'Session Report')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Session Report</h1></div>
<div class="card"><div class="card-body">
<p>Title: {{ $report->title ?? '-' }}</p>
<p>Patient: {{ optional($report->patient)->user->name ?? '-' }}</p>
<p>Status: {{ $report->status ?? '-' }}</p>
</div></div>
@endsection
