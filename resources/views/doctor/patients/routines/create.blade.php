@extends('layouts.app')

@section('title', 'Create Routine - Your Mind Aid')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.routines.index', $patient) }}">Routines</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
            
            <h2 class="mb-1">Create Daily Routine</h2>
            <p class="text-muted">Create structured daily routine for {{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</p>
        </div>
    </div>

    <form action="{{ route('patients.routines.store', $patient) }}" method="POST" id="routineForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Routine Details -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="mb-3">Routine Details</h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">
                                Routine Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="title" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" 
                                   placeholder="e.g., Morning Wellness Routine" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="2" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Purpose and goals of this routine">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="frequency" class="form-label fw-semibold">
                                    Frequency <span class="text-danger">*</span>
                                </label>
                                <select name="frequency" id="frequency" class="form-select" required>
                                    <option value="daily">Daily</option>
                                    <option value="weekdays">Weekdays</option>
                                    <option value="weekends">Weekends</option>
                                    <option value="custom">Custom Schedule</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label fw-semibold">Recommended Start Time</label>
                                <input type="time" name="start_time" id="start_time" class="form-control">
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Activate routine immediately
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Routine Items -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Routine Tasks</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">
                                <i class="bi bi-plus-lg me-1"></i>Add Task
                            </button>
                        </div>

                        <div id="itemsContainer">
                            <!-- Items will be added here -->
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Create Routine
                    </button>
                    <a href="{{ route('patients.routines.index', $patient) }}" class="btn btn-outline-secondary btn-lg ms-2">
                        Cancel
                    </a>
                </div>
            </div>

            <!-- Help Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">
                            <i class="bi bi-lightbulb me-2"></i>Routine Design Tips
                        </h6>
                        <ul class="small mb-0">
                            <li>Start with 5-7 manageable tasks</li>
                            <li>Organize by time of day (morning, afternoon, evening)</li>
                            <li>Include both therapeutic and self-care activities</li>
                            <li>Set realistic time estimates</li>
                            <li>Mark essential tasks as required</li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Sample Tasks</h6>
                        <ul class="small mb-0">
                            <li>Morning meditation (10 min)</li>
                            <li>Mood check-in</li>
                            <li>Take medications</li>
                            <li>Physical exercise (30 min)</li>
                            <li>Evening journaling</li>
                            <li>Gratitude practice</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = 0;

document.getElementById('addItemBtn').addEventListener('click', function() {
    addItem();
});

function addItem() {
    const container = document.getElementById('itemsContainer');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'card mb-3 item-card';
    itemDiv.dataset.index = itemIndex;
    
    itemDiv.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">Task ${itemIndex + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${itemIndex})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Task Title <span class="text-danger">*</span></label>
                    <input type="text" name="items[${itemIndex}][title]" 
                           class="form-control" placeholder="e.g., Morning meditation" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Time of Day</label>
                    <select name="items[${itemIndex}][time_of_day]" class="form-select" required>
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                        <option value="evening">Evening</option>
                        <option value="night">Night</option>
                        <option value="anytime">Anytime</option>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="items[${itemIndex}][description]" rows="2" 
                              class="form-control" placeholder="Brief description of this task"></textarea>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Scheduled Time</label>
                    <input type="time" name="items[${itemIndex}][scheduled_time]" class="form-control">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="number" name="items[${itemIndex}][estimated_minutes]" 
                           class="form-control" placeholder="e.g., 15" min="1">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Required?</label>
                    <div class="form-check form-switch">
                        <input type="checkbox" name="items[${itemIndex}][is_required]" 
                               class="form-check-input" value="1" checked>
                        <label class="form-check-label">Required task</label>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(itemDiv);
    itemIndex++;
}

function removeItem(index) {
    const itemCard = document.querySelector(`[data-index="${index}"]`);
    if (itemCard) {
        itemCard.remove();
    }
}

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
    addItem();
    addItem();
});
</script>
@endsection
