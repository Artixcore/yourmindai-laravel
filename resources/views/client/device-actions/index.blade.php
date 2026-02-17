@extends('client.layout')

@section('title', 'Device Actions - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Device Actions</h4>
    <p class="text-muted mb-0 small">Log device-related actions and wellbeing tracking</p>
</div>

<!-- Add Action Form -->
<div class="card mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0 fw-semibold">Log New Action</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('client.device-actions.store') }}" class="ajax-form" data-target="#device-actions-list" data-prepend="true">
            @csrf
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="form-label small">Action Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="action_type" required>
                        @foreach(\App\Models\DeviceAction::actionTypes() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small">Device (optional)</label>
                    <select class="form-select" name="device_id">
                        <option value="">— None —</option>
                        @foreach($devices as $d)
                        <option value="{{ $d->id }}">{{ $d->device_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small">Note (optional)</label>
                    <input type="text" class="form-control" name="action_note" placeholder="Brief note...">
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-sm" data-loading-text="Logging...">
                    <i class="bi bi-plus-circle me-1"></i>Log Action
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Actions List -->
<div class="card mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0 fw-semibold">Recent Actions</h6>
    </div>
    <div class="card-body p-0" id="device-actions-list">
        @forelse($actions as $action)
        @include('client.device-actions._action_row', ['action' => $action])
        @empty
        <div id="device-actions-empty" class="text-center py-5 text-muted">
            <i class="bi bi-broadcast" style="font-size: 2.5rem;"></i>
            <p class="mt-2 mb-0">No actions logged yet.</p>
            <p class="small">Log your first action above.</p>
        </div>
        @endforelse
    </div>
    @if($actions->hasPages())
    <div class="card-footer bg-white">
        {{ $actions->links() }}
    </div>
    @endif
</div>
@endsection
