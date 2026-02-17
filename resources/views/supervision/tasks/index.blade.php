@extends('supervision.layout')

@section('title', 'Tasks - ' . ($patient->user->name ?? 'Client'))

@section('content')
<div class="mb-4">
    <a href="{{ route('supervision.dashboard') }}" class="btn btn-link text-decoration-none p-0 mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
    <h4 class="fw-bold mb-1">Tasks for {{ $patient->user->name ?? 'Client' }}</h4>
    <p class="text-muted mb-0 small">Mark tasks as verified and add remarks</p>
</div>

<div id="ajax-alerts"></div>

@if($tasks->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-check2-square text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-0">No tasks assigned yet.</p>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($tasks as $task)
    @php
        $verification = $task->verifications->first();
        $isVerified = $verification !== null;
    @endphp
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0" style="min-width: 44px; min-height: 44px;">
                        <label class="d-flex align-items-center justify-content-center m-0" style="min-height: 44px; min-width: 44px; cursor: pointer;">
                            <input type="checkbox" class="form-check-input verify-checkbox" style="width: 24px; height: 24px; cursor: pointer;"
                                {{ $isVerified ? 'checked' : '' }}
                                data-task-id="{{ $task->id }}"
                                data-verify-url="{{ route('supervision.tasks.verify', $task) }}">
                            <span class="visually-hidden">Verified</span>
                        </label>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-semibold">{{ $task->title }}</h6>
                        @if($task->description)
                        <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($task->description, 120) }}</p>
                        @endif
                        <div class="d-flex flex-wrap gap-2 small mb-2">
                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($task->status) }}</span>
                            @if($task->due_date)
                            <span class="text-muted"><i class="bi bi-calendar3 me-1"></i>Due: {{ $task->due_date->format('M d, Y') }}</span>
                            @endif
                            @if($isVerified)
                            <span class="badge bg-info"><i class="bi bi-person-check me-1"></i>Verified</span>
                            @endif
                        </div>
                        <div class="remarks-container" data-task-id="{{ $task->id }}">
                            <label class="form-label small mb-1">Remarks (optional)</label>
                            <textarea class="form-control form-control-sm remarks-input" rows="2" placeholder="Add a remark..."
                                data-task-id="{{ $task->id }}">{{ $verification?->remarks }}</textarea>
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

    function saveVerification(cb, remarksEl) {
        var url = cb.data('verify-url');
        var verified = cb.is(':checked');
        var remarks = remarksEl ? remarksEl.val() : '';

        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), verified: verified ? 1 : 0, remarks: remarks },
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
            if (typeof AppAjax !== 'undefined' && AppAjax.showAlert) AppAjax.showAlert('error', msg);
        });
    }

    $(document).on('change', '.verify-checkbox', function() {
        var cb = $(this);
        var taskId = cb.data('task-id');
        var remarksEl = $('.remarks-input[data-task-id="' + taskId + '"]');
        saveVerification(cb, remarksEl);
    });

    $(document).on('blur', '.remarks-input', function() {
        var remarksEl = $(this);
        var taskId = remarksEl.data('task-id');
        var cb = $('.verify-checkbox[data-task-id="' + taskId + '"]');
        if (cb.is(':checked')) saveVerification(cb, remarksEl);
    });
})();
</script>
@endsection
