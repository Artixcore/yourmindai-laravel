@extends('client.layout')

@section('title', 'My Journal - Your Mind Aid')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h4 class="fw-bold mb-1">My Journal</h4>
        <p class="text-muted mb-0 small">Your personal journal entries</p>
    </div>
    <a href="{{ route('client.journal.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> New Entry
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($journalEntries->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-journal-text fs-1 text-muted"></i>
            <p class="text-muted mb-0 mt-2">No journal entries yet.</p>
            <a href="{{ route('client.journal.create') }}" class="btn btn-primary btn-sm mt-3">Create first entry</a>
        </div>
    </div>
@else
    @foreach($journalEntries as $entry)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-semibold">{{ $entry->entry_date ? \Carbon\Carbon::parse($entry->entry_date)->format('M d, Y') : $entry->created_at->format('M d, Y') }}</span>
                    @if(isset($entry->mood_score))
                        <span class="badge bg-primary">{{ $entry->mood_score }}/10</span>
                    @endif
                </div>
                @if($entry->notes)
                    <p class="text-muted small mb-0">{{ Str::limit($entry->notes, 200) }}</p>
                @endif
            </div>
        </div>
    @endforeach
@endif
@endsection
