@extends('parent.layout')

@section('title', 'Verify Tasks - ' . ($patient->user->name ?? 'Child'))

@section('content')
<div class="mb-4">
    <a href="{{ route('parent.dashboard') }}" class="btn btn-link text-decoration-none p-0 mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
    <h4 class="fw-bold mb-1">Tasks for {{ $patient->user->name ?? 'Child' }}</h4>
    <p class="text-muted mb-0 small">Mark tasks as verified when your child has completed them</p>
</div>

<div id="ajax-alerts"></div>

@if($tasks->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-check2-square text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-0">No tasks assigned yet.</p>
        <p class="text-muted small">Tasks visible to parents will appear here.</p>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($tasks as $task)
    @php
        $isVerified = $task->verifications->isNotEmpty();
    @endphp
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0" style="min-width: 44px; min-height: 44px;">
                        <form class="verify-form d-inline" data-task-id="{{ $task->id }}" data-verified="{{ $isVerified ? '1' : '0' }}">
                            @csrf
                            <input type="hidden" name="verified" value="{{ $isVerified ? '0' : '1' }}">
                            <label class="d-flex align-items-center justify-content-center m-0" style="min-height: 44px; min-width: 44px; cursor: pointer;">
                                <input type="checkbox"
                                    class="form-check-input verify-checkbox"
                                    style="width: 24px; height: 24px; cursor: pointer;"
                                    {{ $isVerified ? 'checked' : '' }}
                                    data-task-id="{{ $task->id }}"
                                    data-verify-url="{{ route('parent.tasks.verify', $task) }}">
                                <span class="visually-hidden">Verified by Parent</span>
                            </label>
                        </form>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-semibold">{{ $task->title }}</h6>
                        @if($task->description)
                        <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($task->description, 120) }}</p>
                        @endif
                        <div class="d-flex flex-wrap gap-2 small">
                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($task->status) }}
                            </span>
                            @if($task->due_date)
                            <span class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>Due: {{ $task->due_date->format('M d, Y') }}
                            </span>
                            @endif
                            @if($task->completed_at)
                            <span class="text-muted">
                                <i class="bi bi-check-circle me-1"></i>Completed: {{ $task->completed_at->format('M d, Y') }}
                            </span>
                            @endif
                            @if($isVerified)
                            <span class="badge bg-info"><i class="bi bi-person-check me-1"></i>Verified</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<script>
(function() {
    if (typeof jQuery === 'undefined') return;
    var $ = jQuery;

    $(document).on('change', '.verify-checkbox', function() {
        var cb = $(this);
        var taskId = cb.data('task-id');
        var url = cb.data('verify-url');
        var verified = cb.is(':checked');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                verified: verified ? 1 : 0
            },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            dataType: 'json'
        })
        .done(function(resp) {
            if (resp && resp.success && typeof AppAjax !== 'undefined' && AppAjax.showAlert) {
                AppAjax.showAlert('success', resp.message || 'Saved.');
            }
        })
        .fail(function(jqXHR) {
            var msg = (jqXHR.responseJSON && jqXHR.responseJSON.message) || 'Something went wrong.';
            cb.prop('checked', !verified);
            if (typeof AppAjax !== 'undefined' && AppAjax.showAlert) {
                AppAjax.showAlert('error', msg);
            }
        });
    });
})();
</script>
@endsection
