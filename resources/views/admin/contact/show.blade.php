@extends('layouts.app')

@section('title', 'Contact Message')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('admin.dashboard')],
                ['label' => 'Contact Messages', 'url' => route('admin.contact.index')],
                ['label' => 'Message #' . $contact->id]
            ]" />
            <h1 class="h3 mb-1 fw-semibold">Contact Message</h1>
            <p class="text-muted mb-0">Message #{{ $contact->id }}</p>
        </div>
        <a href="{{ route('admin.contact.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Messages
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-success alert-dismissible fade show mb-4"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Message Details -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">Message Information</h5>
                <x-badge :variant="$contact->status === 'resolved' ? 'success' : 'warning'">
                    {{ ucfirst($contact->status ?? 'pending') }}
                </x-badge>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Name</label>
                    <p class="fw-medium mb-0">{{ $contact->name }}</p>
                </div>
                
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Email</label>
                    <p class="fw-medium mb-0">
                        <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                    </p>
                </div>
                
                @if($contact->subject)
                <div class="col-12">
                    <label class="form-label small text-muted">Subject</label>
                    <p class="fw-medium mb-0">{{ $contact->subject }}</p>
                </div>
                @endif
                
                <div class="col-12">
                    <label class="form-label small text-muted">Message</label>
                    <div class="border rounded p-3 bg-light" style="white-space: pre-wrap;">{{ $contact->message }}</div>
                </div>
                
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Created</label>
                    <p class="fw-medium mb-0">{{ $contact->created_at->format('M d, Y H:i') }}</p>
                </div>
                
                @if($contact->resolved_at)
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Resolved</label>
                    <p class="fw-medium mb-0">
                        {{ $contact->resolved_at->format('M d, Y H:i') }}
                        @if($contact->resolvedBy)
                            <br><small class="text-muted">by {{ $contact->resolvedBy->name ?? $contact->resolvedBy->email }}</small>
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Admin Notes -->
    @if($contact->admin_notes)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Admin Notes</h5>
        </div>
        <div class="card-body p-4">
            <div style="white-space: pre-wrap;">{{ $contact->admin_notes }}</div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Actions</h5>
        </div>
        <div class="card-body p-4">
            @if($contact->status !== 'resolved')
            <!-- Resolve Form -->
            <form action="{{ route('admin.contact.resolve', $contact) }}" method="POST" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Admin Notes (optional)</label>
                    <textarea name="admin_notes" rows="3" class="form-control" placeholder="Add any notes about resolving this message...">{{ old('admin_notes', $contact->admin_notes) }}</textarea>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i>Mark as Resolved
                </button>
            </form>
            @endif

            <!-- Add/Update Notes Form -->
            <form action="{{ route('admin.contact.notes', $contact) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Admin Notes</label>
                    <textarea name="admin_notes" rows="3" class="form-control" required>{{ old('admin_notes', $contact->admin_notes) }}</textarea>
                    <small class="form-text text-muted">Add or update notes for this message.</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>{{ $contact->admin_notes ? 'Update Notes' : 'Add Notes' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
