@extends('layouts.app')

@section('title', 'Edit Behavior Contingency Plan')

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Patients', 'url' => route('patients.index')],
        ['label' => $patient->name, 'url' => route('patients.show', $patient)],
        ['label' => 'Behavior Contingency Plans', 'url' => route('patients.behavior-contingency-plans.index', $patient)],
        ['label' => 'Edit']
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Edit Behavior Contingency Plan</h1>
    <p class="text-muted mb-0">Update plan details for {{ $plan->title }}</p>
</div>

<form method="POST" action="{{ route('patients.behavior-contingency-plans.update', [$patient, $plan]) }}">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Plan Details</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $plan->title) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="active" {{ old('status', $plan->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="archived" {{ old('status', $plan->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="starts_at" value="{{ old('starts_at', $plan->starts_at->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="ends_at" value="{{ old('ends_at', $plan->ends_at?->format('Y-m-d')) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Behavior Items</h5>
            <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                <i class="bi bi-plus-circle me-1"></i>Add Item
            </button>
        </div>
        <div class="card-body" id="itemsContainer">
            @foreach($plan->items as $i => $item)
            <div class="item-row mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Item {{ $i + 1 }}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label small">Target Behavior <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="items[{{ $i }}][target_behavior]" value="{{ old('items.'.$i.'.target_behavior', $item->target_behavior) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Condition/Stimulus <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="items[{{ $i }}][condition_stimulus]" value="{{ old('items.'.$i.'.condition_stimulus', $item->condition_stimulus) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Reward (if followed)</label>
                        <input type="text" class="form-control" name="items[{{ $i }}][reward_if_followed]" value="{{ old('items.'.$i.'.reward_if_followed', $item->reward_if_followed) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Punishment (if not followed)</label>
                        <input type="text" class="form-control" name="items[{{ $i }}][punishment_if_not_followed]" value="{{ old('items.'.$i.'.punishment_if_not_followed', $item->punishment_if_not_followed) }}">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update Plan</button>
        <a href="{{ route('patients.behavior-contingency-plans.index', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = {{ $plan->items->count() }};
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const div = document.createElement('div');
        div.className = 'item-row mb-3 p-3 border rounded';
        div.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-2"><strong>Item ' + (itemCount + 1) + '</strong><button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i class="bi bi-trash"></i></button></div><div class="row g-2"><div class="col-12"><label class="form-label small">Target Behavior <span class="text-danger">*</span></label><input type="text" class="form-control" name="items[' + itemCount + '][target_behavior]" required></div><div class="col-12"><label class="form-label small">Condition/Stimulus <span class="text-danger">*</span></label><input type="text" class="form-control" name="items[' + itemCount + '][condition_stimulus]" required></div><div class="col-md-6"><label class="form-label small">Reward (if followed)</label><input type="text" class="form-control" name="items[' + itemCount + '][reward_if_followed]"></div><div class="col-md-6"><label class="form-label small">Punishment (if not followed)</label><input type="text" class="form-control" name="items[' + itemCount + '][punishment_if_not_followed]"></div></div>';
        container.appendChild(div);
        div.querySelector('.remove-item-btn').addEventListener('click', function() { div.remove(); });
        itemCount++;
    });
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.addEventListener('click', function() { this.closest('.item-row').remove(); });
    });
});
</script>
@endpush
@endsection
