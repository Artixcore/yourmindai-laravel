@extends('layouts.app')

@section('title', 'Contact Messages')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Contact Messages']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Contact Messages</h1>
        <p class="text-muted mb-0">Manage contact form submissions</p>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.contact.index') }}" class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div class="col-12 col-md-8 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Apply Filter</button>
                @if(request()->has('status'))
                    <a href="{{ route('admin.contact.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table -->
@if($messages->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Name</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Subject</th>
                            <th class="border-0">Message Preview</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Created</th>
                            <th class="border-0 text-end" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $message->name }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $message->email }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $message->subject ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ Str::limit($message->message, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <x-badge :variant="$message->status === 'resolved' ? 'success' : 'warning'">
                                        {{ ucfirst($message->status ?? 'pending') }}
                                    </x-badge>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $message->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.contact.show', $message) }}" 
                                       class="btn btn-sm btn-link text-primary p-1" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($messages->hasPages())
                <div class="card-footer bg-transparent border-top py-3">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
@else
    <!-- Empty state -->
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-envelope text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No messages found</h5>
            <p class="text-muted mb-0">
                @if(request()->has('status'))
                    No messages match your filter criteria.
                @else
                    No contact messages have been received yet.
                @endif
            </p>
        </div>
    </div>
@endif
@endsection
