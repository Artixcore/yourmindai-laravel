@extends('layouts.app')

@section('title', 'Psychometric Scales')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Psychometric Scales']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Psychometric Scales</h1>
        <p class="text-muted mb-0">Manage assessment scales and questionnaires</p>
    </div>
    <a href="{{ route('psychometric-scales.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Create New Scale
    </a>
</div>

<!-- Scales List -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($scales->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No scales created yet</h5>
            <p class="text-muted mb-0">Create your first psychometric scale to get started.</p>
            <a href="{{ route('psychometric-scales.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-2"></i>Create Scale
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Name</th>
                        <th class="border-0">Category</th>
                        <th class="border-0">Questions</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Created By</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scales as $scale)
                    <tr>
                        <td class="ps-4">
                            <strong>{{ $scale->name }}</strong>
                            @if($scale->description)
                            <br>
                            <small class="text-muted">{{ Str::limit($scale->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($scale->category)
                            <span class="badge bg-secondary">{{ $scale->category }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {{ count($scale->questions ?? []) }} questions
                        </td>
                        <td>
                            @if($scale->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            {{ $scale->createdByDoctor->name ?? 'System' }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('psychometric-scales.show', $scale) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('psychometric-scales.edit', $scale) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('psychometric-scales.destroy', $scale) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this scale?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
