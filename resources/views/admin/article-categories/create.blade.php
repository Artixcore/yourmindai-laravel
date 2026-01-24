@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.article-categories.index') }}" class="text-decoration-none">← Back</a>
        <h1 class="h2 fw-bold mt-2">Create Category</h1>
    </div>

    <form method="POST" action="{{ route('admin.article-categories.store') }}">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                    <small class="text-muted">Leave blank to auto-generate</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('admin.article-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
