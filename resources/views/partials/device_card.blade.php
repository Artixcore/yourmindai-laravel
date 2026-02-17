<div id="device-card-{{ $device->id }}" class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">{{ $device->device_name }}</h6>
                <div class="small text-muted mb-2">
                    <div><i class="bi bi-device-hdd me-1"></i> {{ ucfirst($device->device_type) }}</div>
                    @if($device->os_type)
                    <div><i class="bi bi-window me-1"></i> {{ $device->os_type }} {{ $device->os_version ?? '' }}</div>
                    @endif
                    @if($device->app_version)
                    <div><i class="bi bi-app me-1"></i> App v{{ $device->app_version }}</div>
                    @endif
                </div>
                <div class="small">
                    @if($device->is_active)
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-secondary">Inactive</span>
                    @endif
                    @if($device->last_active_at)
                    <span class="text-muted ms-2">
                        Last active: {{ $device->last_active_at->diffForHumans() }}
                    </span>
                    @endif
                </div>
                <div class="small text-muted mt-2">
                    Registered: {{ $device->registered_at?->format('M d, Y') ?? '—' }}
                    @if($device->device_source === 'manual')
                        <span class="badge bg-secondary ms-1">Manual</span>
                    @endif
                </div>
                @if($device->notes)
                <div class="small text-muted mt-1">{{ Str::limit($device->notes, 60) }}</div>
                @endif
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDeviceModal{{ $device->id }}" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('client.devices.destroy', $device->id) }}" class="ajax-form" data-target="#device-card-{{ $device->id }}" onsubmit="return confirm('Are you sure you want to remove this device?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" data-loading-text="Removing..." title="Remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
