@extends('layouts.app')

@section('title', 'Session Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 fw-bold text-stone-900 mb-0">Session Reports</h1>
        <a href="{{ route('session-reports.create') }}" class="btn btn-primary">New report</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted small">Total</span><div class="h4 mb-0">{{ $stats['total'] }}</div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted small">Finalized</span><div class="h4 mb-0">{{ $stats['finalized'] }}</div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted small">Draft</span><div class="h4 mb-0">{{ $stats['draft'] }}</div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted small">This week</span><div class="h4 mb-0">{{ $stats['this_week'] }}</div></div></div></div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                @if($patients->isNotEmpty())
                    <div class="col-md-2">
                        <select name="patient_id" class="form-select form-select-sm">
                            <option value="">All patients</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ request('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->full_name ?? $p->user->name ?? $p->id }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="finalized" {{ request('status') == 'finalized' ? 'selected' : '' }}>Finalized</option>
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-primary">Filter</button></div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Patient</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $r)
                            <tr>
                                <td>{{ $r->title }}</td>
                                <td>{{ $r->patient->full_name ?? $r->patient->user->name ?? '—' }}</td>
                                <td><span class="badge {{ $r->finalized_at ? 'bg-success' : 'bg-secondary' }}">{{ $r->finalized_at ? 'Finalized' : $r->status }}</span></td>
                                <td>{{ $r->created_at->format('M d, Y') }}</td>
                                <td><a href="{{ route('session-reports.show', $r) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">No reports yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $reports->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
