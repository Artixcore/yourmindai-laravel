@extends('layouts.app')
@section('title', 'Feedback')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Feedback</h1></div>
<div class="card"><div class="card-body">
<p>Total: {{ $stats['total'] ?? 0 }}</p>
@if($feedbacks->isEmpty())<p class="mb-0">No feedback.</p>
@else
<table class="table"><thead><tr><th>Patient</th><th>Date</th><th>Source</th></tr></thead><tbody>
@foreach($feedbacks as $f)<tr><td>{{ optional(optional($f->patient)->user)->name ?? '-' }}</td><td>{{ $f->feedback_date ?? '-' }}</td><td>{{ $f->source ?? '-' }}</td></tr>@endforeach
</tbody></table>{{ $feedbacks->links() }}
@endif
</div></div>
@endsection
