@extends('layouts.app')

@section('title', 'Edit Contingency Plan')

@section('content')
<div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patient->name, 'url' => route('patients.show', $patient)],
            ['label' => 'Contingency Plans', 'url' => route('patients.contingency-plans.index', $patient)],
            ['label' => 'Edit']
        ]" />
    <h1 class="h3 mb-1 fw-semibold">Edit Contingency Plan</h1>
    <p class="text-muted mb-0">Update emergency plan details</p>
</div>

<form method="POST" action="{{ route('patients.contingency-plans.update', ['patient' => $patient, 'contingencyPlan' => $contingencyPlan]) }}">
    @csrf
    @method('PUT')
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Basic Information</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Plan Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" value="{{ old('title', $contingencyPlan->title) }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <option value="active" {{ old('status', $contingencyPlan->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $contingencyPlan->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Trigger Conditions</h5>
            <button type="button" class="btn btn-sm btn-primary" onclick="addTriggerCondition()">
                <i class="bi bi-plus-circle me-1"></i>Add Condition
            </button>
        </div>
        <div class="card-body">
            <div id="triggerConditionsContainer">
                @if($contingencyPlan->trigger_conditions)
                    @foreach($contingencyPlan->trigger_conditions as $index => $condition)
                    <div class="trigger-condition-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong>Condition {{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.trigger-condition-item').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <textarea class="form-control" name="trigger_conditions[{{ $index }}][description]" rows="2" required>{{ is_string($condition) ? $condition : ($condition['description'] ?? '') }}</textarea>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Actions</h5>
            <button type="button" class="btn btn-sm btn-primary" onclick="addAction()">
                <i class="bi bi-plus-circle me-1"></i>Add Action
            </button>
        </div>
        <div class="card-body">
            <div id="actionsContainer">
                @if($contingencyPlan->actions)
                    @foreach($contingencyPlan->actions as $index => $action)
                    <div class="action-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong>Action {{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.action-item').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <textarea class="form-control" name="actions[{{ $index }}][description]" rows="2" required>{{ is_string($action) ? $action : ($action['description'] ?? '') }}</textarea>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Emergency Contacts</h5>
            <button type="button" class="btn btn-sm btn-primary" onclick="addEmergencyContact()">
                <i class="bi bi-plus-circle me-1"></i>Add Contact
            </button>
        </div>
        <div class="card-body">
            <div id="emergencyContactsContainer">
                @if($contingencyPlan->emergency_contacts)
                    @foreach($contingencyPlan->emergency_contacts as $index => $contact)
                    <div class="emergency-contact-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong>Contact {{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.emergency-contact-item').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="emergency_contacts[{{ $index }}][name]" value="{{ $contact['name'] ?? '' }}" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="emergency_contacts[{{ $index }}][phone]" value="{{ $contact['phone'] ?? '' }}" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Relationship</label>
                                <input type="text" class="form-control" name="emergency_contacts[{{ $index }}][relationship]" value="{{ $contact['relationship'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('patients.contingency-plans.index', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Update Plan</button>
    </div>
</form>

@push('scripts')
<script>
(function() {
    'use strict';
    
    let triggerConditionCount = {{ count($contingencyPlan->trigger_conditions ?? []) }};
    let actionCount = {{ count($contingencyPlan->actions ?? []) }};
    let emergencyContactCount = {{ count($contingencyPlan->emergency_contacts ?? []) }};
    
    function addTriggerCondition() {
        try {
            const container = document.getElementById('triggerConditionsContainer');
            if (!container) return;
            
            const conditionDiv = document.createElement('div');
            conditionDiv.className = 'trigger-condition-item mb-3 p-3 border rounded';
            conditionDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong>Condition ${triggerConditionCount + 1}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.trigger-condition-item').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <textarea class="form-control" name="trigger_conditions[${triggerConditionCount}][description]" rows="2" required></textarea>
            `;
            container.appendChild(conditionDiv);
            triggerConditionCount++;
        } catch (e) {
            // Silently fail
        }
    }
    
    function addAction() {
        try {
            const container = document.getElementById('actionsContainer');
            if (!container) return;
            
            const actionDiv = document.createElement('div');
            actionDiv.className = 'action-item mb-3 p-3 border rounded';
            actionDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong>Action ${actionCount + 1}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.action-item').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <textarea class="form-control" name="actions[${actionCount}][description]" rows="2" required></textarea>
            `;
            container.appendChild(actionDiv);
            actionCount++;
        } catch (e) {
            // Silently fail
        }
    }
    
    function addEmergencyContact() {
        try {
            const container = document.getElementById('emergencyContactsContainer');
            if (!container) return;
            
            const contactDiv = document.createElement('div');
            contactDiv.className = 'emergency-contact-item mb-3 p-3 border rounded';
            contactDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong>Contact ${emergencyContactCount + 1}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.emergency-contact-item').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="emergency_contacts[${emergencyContactCount}][name]" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="emergency_contacts[${emergencyContactCount}][phone]" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Relationship</label>
                        <input type="text" class="form-control" name="emergency_contacts[${emergencyContactCount}][relationship]">
                    </div>
                </div>
            `;
            container.appendChild(contactDiv);
            emergencyContactCount++;
        } catch (e) {
            // Silently fail
        }
    }
    
    // Make functions globally accessible for inline handlers
    window.addTriggerCondition = addTriggerCondition;
    window.addAction = addAction;
    window.addEmergencyContact = addEmergencyContact;
})();
</script>
@endpush
@endsection
