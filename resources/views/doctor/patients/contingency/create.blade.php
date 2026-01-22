@extends('layouts.app')

@section('title', 'Create Contingency Plan')

@section('content')
<div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patient->name, 'url' => route('patients.show', $patient)],
            ['label' => 'Contingency Plans', 'url' => route('patients.contingency-plans.index', $patient)],
            ['label' => 'Create']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Create Contingency Plan</h1>
        <p class="text-muted mb-0">Define emergency procedures for {{ $patient->name }}</p>
</div>

<form method="POST" action="{{ route('patients.contingency-plans.store', $patient) }}">
    @csrf
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Basic Information</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Plan Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" value="{{ old('title') }}" required placeholder="e.g., Crisis Intervention Plan">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                <div class="trigger-condition-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong>Condition 1</strong>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.trigger-condition-item').remove()">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <textarea class="form-control" name="trigger_conditions[0][description]" rows="2" required placeholder="Describe when this plan should be activated"></textarea>
                </div>
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
                <div class="action-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong>Action 1</strong>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.action-item').remove()">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <textarea class="form-control" name="actions[0][description]" rows="2" required placeholder="Describe what should happen when this plan is activated"></textarea>
                </div>
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
                <!-- Emergency contacts will be added here -->
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('patients.contingency-plans.index', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Plan</button>
    </div>
</form>

@push('scripts')
<script>
(function() {
    'use strict';
    
    let triggerConditionCount = 1;
    let actionCount = 1;
    let emergencyContactCount = 0;
    
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
                <textarea class="form-control" name="trigger_conditions[${triggerConditionCount}][description]" rows="2" required placeholder="Describe when this plan should be activated"></textarea>
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
                <textarea class="form-control" name="actions[${actionCount}][description]" rows="2" required placeholder="Describe what should happen when this plan is activated"></textarea>
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
                        <input type="text" class="form-control" name="emergency_contacts[${emergencyContactCount}][relationship]" placeholder="e.g., Parent, Guardian">
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
