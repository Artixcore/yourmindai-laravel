@extends('layouts.app')
@section('title', 'Practice Progressions')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Practice Progressions</h1></div>
<div class="card"><div class="card-body">
<p>Total: {{ $stats['total'] ?? 0 }}</p>
@if($progressions->isEmpty())<p class="mb-0">No progressions.</p>
@else
<table class="table"><thead><tr><th>Patient</th><th>Date</th><th>Status</th></tr></thead><tbody>
@foreach($progressions as $p)<tr><td>{{ optional(optional($p->patient)->user)->name ?? '-' }}</td><td>{{ $p->progress_date ?? '-' }}</td><td>{{ $p->status ?? '-' }}</td></tr>@endforeach
</tbody></table>{{ $progressions->links() }}
@endif
</div></div>
@endsection
