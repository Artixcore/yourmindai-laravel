@extends('layouts.app')

@section('title', 'Review Questions')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold">Review Questions</h1>
            <p class="text-muted">Manage questions for patient reviews</p>
        </div>
        <a href="{{ route('admin.review-questions.create') }}" class="btn btn-primary">
            Add Question
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="applies_to" class="form-select">
                        <option value="">All Types</option>
                        <option value="doctor" {{ request('applies_to') === 'doctor' ? 'selected' : '' }}>Doctor Reviews</option>
                        <option value="session" {{ request('applies_to') === 'session' ? 'selected' : '' }}>Session Reviews</option>
                        <option value="both" {{ request('applies_to') === 'both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="is_active" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('admin.review-questions.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Questions List -->
    @if ($questions->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Order</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Applies To</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $question)
                            <tr>
                                <td>{{ $question->order }}</td>
                                <td>{{ Str::limit($question->question_text, 80) }}</td>
                                <td><span class="badge bg-info">{{ str_replace('_', ' ', ucfirst($question->question_type)) }}</span></td>
                                <td><span class="badge bg-secondary">{{ ucfirst($question->applies_to) }}</span></td>
                                <td>
                                    <span class="badge {{ $question->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $question->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.review-questions.edit', $question) }}" class="btn btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.review-questions.destroy', $question) }}" 
                                              onsubmit="return confirm('Are you sure?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">No questions found</p>
            </div>
        </div>
    @endif
</div>
@endsection
