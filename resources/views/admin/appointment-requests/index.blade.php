@extends('layouts.app')

@section('title', 'Appointment Requests')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => ($isAdmin ?? true) ? route('admin.dashboard') : route('dashboard')],
            ['label' => 'Appointment Requests']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Appointment Requests</h1>
        <p class="text-muted mb-0">
            @if($isAdmin ?? true)
                Manage public appointment booking requests
            @else
                View appointment requests assigned to you
            @endif
        </p>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ ($isAdmin ?? true) ? route('admin.appointment-requests.index') : route('doctors.appointment-requests.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Name, email, or phone..." class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-muted">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-muted">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Requests List -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($appointmentRequests->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No appointment requests</h5>
            <p class="text-muted mb-0">Appointment requests from the public will appear here.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Name</th>
                        <th class="border-0">Email</th>
                        <th class="border-0">Phone</th>
                        <th class="border-0">Preferred Date</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Created</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointmentRequests as $request)
                    <tr>
                        <td class="ps-4">
                            <strong>{{ $request->first_name }} {{ $request->last_name }}</strong>
                        </td>
                        <td>{{ $request->email }}</td>
                        <td>{{ $request->phone ?? '-' }}</td>
                        <td>
                            {{ $request->preferred_date->format('M d, Y') }}
                            @if($request->preferred_time)
                            <br><small class="text-muted">{{ $request->preferred_time }}</small>
                            @endif
                        </td>
                        <td>
                            @if($request->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($request->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                            @elseif($request->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                            @elseif($request->status === 'converted')
                            <span class="badge bg-info">Converted</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ ($isAdmin ?? true) ? route('admin.appointment-requests.show', $request) : route('doctors.appointment-requests.show', $request) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer bg-white border-0">
            {{ $appointmentRequests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
