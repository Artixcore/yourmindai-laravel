@extends('layouts.app')

@section('title', $paper->title)

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Papers', 'url' => route('doctors.papers.index')],
                ['label' => $paper->title]
            ]" />
            <h1 class="h3 mb-1 fw-semibold">{{ $paper->title }}</h1>
            <p class="text-muted mb-0">Document Details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('doctors.papers.download', $paper) }}" class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Download
            </a>
            <a href="{{ route('doctors.papers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Papers
            </a>
        </div>
    </div>

    <!-- Paper Information -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Document Information</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small text-muted">Title</label>
                    <p class="fw-medium mb-0">{{ $paper->title }}</p>
                </div>
                
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Category</label>
                    <div>
                        <x-badge :variant="$paper->category === 'license' ? 'success' : ($paper->category === 'certificate' ? 'info' : 'default')">
                            {{ ucfirst($paper->category) }}
                        </x-badge>
                    </div>
                </div>
                
                @if($paper->issued_date)
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Issued Date</label>
                    <p class="fw-medium mb-0">{{ $paper->issued_date->format('M d, Y') }}</p>
                </div>
                @endif
                
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Uploaded</label>
                    <p class="fw-medium mb-0">{{ $paper->created_at->format('M d, Y') }}</p>
                </div>
                
                @if($paper->notes)
                <div class="col-12">
                    <label class="form-label small text-muted">Notes</label>
                    <div class="border rounded p-3 bg-light" style="white-space: pre-wrap;">{{ $paper->notes }}</div>
                </div>
                @endif
                
                @if($paper->file_path)
                <div class="col-12">
                    <label class="form-label small text-muted">File</label>
                    <div>
                        <a href="{{ route('doctors.papers.download', $paper) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download me-2"></i>Download File
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex gap-2">
                <a href="{{ route('doctors.papers.edit', $paper) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Document
                </a>
                <form action="{{ route('doctors.papers.destroy', $paper) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Document
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
