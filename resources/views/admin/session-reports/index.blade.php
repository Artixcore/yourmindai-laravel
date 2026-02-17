@extends('layouts.app')
@section('title', 'Session Reports')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Session Reports</h1></div>
<div class="card"><div class="card-body">
<p>Total: {{ $stats['total'] ?? 0 }}</p>
@if($reports->isEmpty())<p class="mb-0">No reports.</p>
@else
<table class="table"><thead><tr><th>Title</th><th>Patient</th><th>Status</th></tr></thead><tbody>
@foreach($reports as $r)<tr><td>{{ $r->title ?? '-' }}</td><td>{{ optional(optional($r->patient)->user)->name ?? '-' }}</td><td>{{ $r->status ?? '-' }}</td></tr>@endforeach
</tbody></table>{{ $reports->links() }}
@endif
</div></div>
@endsection
