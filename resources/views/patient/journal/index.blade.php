@extends('layouts.app')

@section('title', 'My Journal - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 fw-bold text-stone-900 mb-2">My Journal</h1>
            <p class="text-stone-600 mb-0">Your personal journal entries</p>
        </div>
        <a href="{{ route('patient.journal.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            New Entry
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($journalEntries->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <i class="bi bi-journal-text fs-1 text-stone-300 mb-3"></i>
                <p class="text-stone-500 mb-0">No journal entries yet.</p>
                <p class="small text-stone-400 mt-2 mb-0">Start recording your thoughts and feelings.</p>
                <a href="{{ route('patient.journal.create') }}" class="btn btn-primary mt-3">
                    Create First Entry
                </a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($journalEntries as $entry)
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <h5 class="h6 fw-semibold text-stone-900 mb-1">
                                        {{ $entry->created_at->format('M d, Y') }}
                                    </h5>
                                    <p class="small text-stone-500 mb-0">
                                        {{ $entry->created_at->format('h:i A') }}
                                    </p>
                                </div>
                                @if(isset($entry->mood_score))
                                    <div class="text-center">
                                        <span class="h4 fw-bold text-patient-primary">{{ $entry->mood_score }}</span>
                                        <span class="small text-stone-500">/10</span>
                                    </div>
                                @endif
                            </div>
                            
                            @if($entry->notes)
                                <p class="text-stone-600 mb-0" style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $entry->notes }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
