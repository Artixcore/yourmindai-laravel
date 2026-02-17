@extends('layouts.app')
@section('title', 'Progression Details')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Progression Details</h1></div>
<div class="card"><div class="card-body">
<p>Patient: {{ optional(optional($progression->patient)->user)->name ?? '-' }}</p>
<p>Date: {{ $progression->progress_date ?? '-' }}</p>
<p>Status: {{ $progression->status ?? '-' }}</p>
</div></div>
@endsection
