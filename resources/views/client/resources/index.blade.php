@extends('client.layout')

@section('title', 'Resources & Feedback - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Resources</h4>
    <p class="text-muted mb-0 small">Videos and PDFs from your doctor, and send feedback about the app</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Resources from Doctor -->
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">From your doctor</h6>
    </div>
    <div class="card-body">
        @if($resources->isEmpty())
            <p class="text-muted mb-0">No resources shared yet. Your doctor will add videos and PDFs here.</p>
        @else
            @foreach($resources as $resource)
                <div class="mb-3">
                    <x-patient.resource-card :resource="$resource" />
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- Send feedback about the app -->
<div class="card">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">Send feedback about the app</h6>
    </div>
    <div class="card-body">
        @if($patientProfile)
            <form action="{{ route('client.feedback.store') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label small">Your feedback</label>
                    <textarea name="feedback_text" class="form-control" rows="3" placeholder="Tell us what you think of the app..." required maxlength="5000"></textarea>
                    @error('feedback_text')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="mb-2">
                    <label class="form-label small">Rating (optional, 1–5)</label>
                    <select name="rating" class="form-select form-select-sm" style="max-width: 80px;">
                        <option value="">—</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Submit feedback</button>
            </form>
        @else
            <p class="text-muted mb-0">Your profile must be linked to submit feedback. Please contact support if you need help.</p>
        @endif
    </div>
</div>
@endsection
