@extends('client.layout')

@section('title', 'Sleep Hygiene - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Sleep Hygiene</h4>
    <p class="text-muted mb-0 small">Daily checklist for better sleep</p>
</div>

<!-- Date selector -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('client.sleep-hygiene.index') }}" class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 small">Date:</label>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ $logDate }}" style="max-width: 160px;" onchange="this.form.submit()">
        </form>
    </div>
</div>

@if($items->isNotEmpty())
    @php
        $completedCount = $items->countBy(fn($i) => ($logs->get($i->id)?->is_completed ?? false))->get(true, 0);
        $totalCount = $items->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="stats-card">
                <div class="number">{{ $completedCount }}/{{ $totalCount }}</div>
                <div class="label">Completed</div>
            </div>
        </div>
        <div class="col-6">
            <div class="stats-card">
                <div class="number">{{ $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0 }}%</div>
                <div class="label">Progress</div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body p-0">
            @foreach($items as $item)
                @php $log = $logs->get($item->id); @endphp
                @include('client.sleep-hygiene._item', ['item' => $item, 'log' => $log, 'logDate' => $logDate])
            @endforeach
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-moon-stars text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">No sleep hygiene items configured</p>
        </div>
    </div>
@endif
@endsection

