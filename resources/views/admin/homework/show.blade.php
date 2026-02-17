@extends('layouts.app')
@section('title', 'Homework Details')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Homework Details</h1></div>
<div class="card"><div class="card-body">
<p>Type: {{ $homework->homework_type ?? '-' }}</p>
<p>Patient: {{ optional(optional($homework->patient)->user)->name ?? '-' }}</p>
<p>Status: {{ $homework->status ?? '-' }}</p>
</div></div>
@endsection
