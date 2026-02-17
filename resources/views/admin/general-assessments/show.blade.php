@extends('layouts.app')
@section('title', 'Assessment Details')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Assessment Details</h1></div>
<div class="card"><div class="card-body">
<p><strong>Patient:</strong> {{ optional(optional($assessment->patient)->user)->name ?? '-' }}</p>
<p><strong>Status:</strong> {{ $assessment->status ?? '-' }}</p>
</div></div>
@endsection
