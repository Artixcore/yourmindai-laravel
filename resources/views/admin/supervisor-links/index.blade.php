@extends('layouts.app')

@section('title', 'Supervisor Links')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[['label' => 'Home', 'url' => route('admin.dashboard')], ['label' => 'Supervisor Links']]" />
        <h1 class="h3 mb-1 fw-semibold">Supervisor Links</h1>
        <p class="text-muted mb-0">Link supervision accounts to clients for task verification</p>
    </div>
    <a href="{{ route('admin.supervisor-links.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Link
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.supervisor-links.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Supervisor</label>
                <select name="supervisor_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($supervisors as $s)
                    <option value="{{ $s->id }}" {{ request('supervisor_id') == $s->id ? 'selected' : '' }}>{{ $s->name ?? $s->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Client</label>
                <select name="patient_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($patients as $pt)
                    <option value="{{ $pt->id }}" {{ request('patient_id') == $pt->id ? 'selected' : '' }}>{{ optional($pt->user)->name ?? $pt->full_name ?? 'Patient' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
</div>

@if($links->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-shield-check text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-0">No supervisor links found.</p>
        <a href="{{ route('admin.supervisor-links.create') }}" class="btn btn-primary mt-3">Add Link</a>
    </div>
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">Supervisor</th>
                        <th class="border-0">Client</th>
                        <th class="border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($links as $link)
                    <tr>
                        <td>{{ optional($link->supervisor)->name ?? optional($link->supervisor)->email ?? 'N/A' }}</td>
                        <td>{{ optional($link->patient->user)->name ?? optional($link->patient)->full_name ?? 'N/A' }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.supervisor-links.destroy', $link) }}" class="d-inline" onsubmit="return confirm('Remove this link?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $links->links() }}</div>
@endif
@endsection
