@extends('layouts.app')

@section('title', 'Edit Document')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <!-- Page Header -->
    <div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Papers', 'url' => route('doctors.papers.index')],
            ['label' => $paper->title, 'url' => route('doctors.papers.show', $paper)],
            ['label' => 'Edit']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Edit Document</h1>
        <p class="text-muted mb-0">Update document information</p>
    </div>

    <!-- Form -->
    <div class="card border-0 shadow-sm">
        <form action="{{ route('doctors.papers.update', $paper) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $paper->title) }}" 
                               class="form-control @error('title') is-invalid @enderror" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="">Select Category</option>
                            <option value="license" {{ old('category', $paper->category) == 'license' ? 'selected' : '' }}>License</option>
                            <option value="certificate" {{ old('category', $paper->category) == 'certificate' ? 'selected' : '' }}>Certificate</option>
                            <option value="other" {{ old('category', $paper->category) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Issued Date</label>
                        <input type="date" name="issued_date" value="{{ old('issued_date', $paper->issued_date ? $paper->issued_date->format('Y-m-d') : '') }}" 
                               class="form-control @error('issued_date') is-invalid @enderror">
                        @error('issued_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $paper->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">File (optional - leave empty to keep current file)</label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
                               class="form-control @error('file') is-invalid @enderror">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($paper->file_path)
                            <small class="form-text text-muted">Current file: {{ basename($paper->file_path) }}</small>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center py-3">
                <a href="{{ route('doctors.papers.show', $paper) }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Update Document
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
