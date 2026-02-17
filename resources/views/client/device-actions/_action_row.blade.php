<div class="border-bottom p-3 device-action-row">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <span class="badge bg-primary me-2">{{ \App\Models\DeviceAction::actionTypes()[$action->action_type] ?? $action->action_type }}</span>
            @if($action->device)
            <span class="small text-muted">{{ $action->device->device_name }}</span>
            @endif
            <div class="small text-muted mt-1">{{ $action->created_at->format('M d, Y H:i') }}</div>
            @if($action->action_note)
            <div class="small mt-1">{{ $action->action_note }}</div>
            @endif
        </div>
    </div>
</div>
