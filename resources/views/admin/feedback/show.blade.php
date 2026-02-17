@extends('layouts.app')
@section('title', 'Feedback Details')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Feedback Details</h1></div>
<div class="card"><div class="card-body">
<p>Patient: {{ optional(optional($feedback->patient)->user)->name ?? '-' }}</p>
<p>Date: {{ $feedback->feedback_date ?? '-' }}</p>
<p>Text: {{ Str::limit($feedback->feedback_text ?? '', 200) }}</p>
</div></div>
@endsection
