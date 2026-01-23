@extends('layouts.app')

@section('title', 'Sessions')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Sessions']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Therapy Sessions</h1>
        <p class="text-muted mb-0">Manage all therapy sessions for your patients</p>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('doctors.sessions.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small">Patient</label>
                <select name="patient_id" class="form-select form-select-sm">
                    <option value="">All Patients</option>
                    @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sessions List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">All Sessions</h5>
    </div>
    <div class="card-body p-0">
        @if($sessions->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-calendar-check text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No sessions found</h5>
            <p class="text-muted mb-0">Create sessions for your patients to start tracking therapy progress.</p>
            <div class="mt-3">
                <a href="{{ route('patients.index') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>Go to Patients
                </a>
            </div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Patient</th>
                        <th class="border-0">Session Title</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Days</th>
                        <th class="border-0">Created</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div>
                                    <strong>{{ $session->patient->name ?? 'Unknown Patient' }}</strong>
                                    @if($session->patient)
                                    <br>
                                    <small class="text-muted">{{ $session->patient->email }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>{{ $session->title }}</strong>
                            @if($session->notes)
                            <br>
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($session->notes, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($session->status === 'active')
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Closed</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $session->days->count() }} day{{ $session->days->count() !== 1 ? 's' : '' }}</span>
                        </td>
                        <td>
                            {{ $session->created_at->format('M d, Y') }}
                            <br>
                            <small class="text-muted">{{ $session->created_at->format('H:i') }}</small>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex align-items-center gap-2 justify-content-end">
                                <a href="{{ route('patients.sessions.show', [$session->patient, $session]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('patients.show', $session->patient) }}" class="btn btn-sm btn-outline-secondary" title="Go to Patient">
                                    <i class="bi bi-person"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer bg-white border-0">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Patients
    </a>
</div>
@endsection
