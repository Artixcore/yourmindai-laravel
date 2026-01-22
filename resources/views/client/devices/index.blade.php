@extends('client.layout')

@section('title', 'My Devices - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">My Devices</h4>
    <p class="text-muted mb-0 small">Manage your registered devices</p>
</div>

<!-- Register New Device Button -->
<div class="card mb-3">
    <div class="card-body text-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerDeviceModal">
            <i class="bi bi-plus-circle me-2"></i>
            Register New Device
        </button>
    </div>
</div>

<!-- Devices List -->
@if($devices->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-phone display-1 text-muted mb-3"></i>
        <p class="text-muted mb-0">No devices registered yet.</p>
        <p class="text-muted small">Register your first device to get started.</p>
    </div>
</div>
@else
@foreach($devices as $device)
<div class="card mb-3">
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
                    Registered: {{ $device->registered_at->format('M d, Y') }}
                </div>
            </div>
            <form method="POST" action="{{ route('client.devices.destroy', $device->id) }}" class="ms-2" onsubmit="return confirm('Are you sure you want to remove this device?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif

<!-- Register Device Modal -->
<div class="modal fade" id="registerDeviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register New Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('client.devices.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Device Name</label>
                        <input type="text" class="form-control" name="device_name" id="device_name" required placeholder="e.g., My iPhone">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Device Type</label>
                        <select class="form-select" name="device_type" id="device_type" required>
                            <option value="mobile">Mobile</option>
                            <option value="tablet">Tablet</option>
                            <option value="desktop">Desktop</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Device Identifier</label>
                        <input type="text" class="form-control" name="device_identifier" id="device_identifier" required readonly>
                        <small class="text-muted">This will be auto-generated</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Operating System</label>
                        <input type="text" class="form-control" name="os_type" id="os_type" placeholder="e.g., iOS, Android">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">OS Version</label>
                        <input type="text" class="form-control" name="os_version" id="os_version" placeholder="e.g., 17.0">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">App Version</label>
                        <input type="text" class="form-control" name="app_version" id="app_version" placeholder="e.g., 1.0.0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Device</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/client-device-detection.js') }}"></script>
<script>
    // Auto-detect device info when modal opens
    document.getElementById('registerDeviceModal').addEventListener('show.bs.modal', function() {
        if (typeof detectDeviceInfo === 'function') {
            const deviceInfo = detectDeviceInfo();
            document.getElementById('device_name').value = deviceInfo.name;
            document.getElementById('device_type').value = deviceInfo.type;
            document.getElementById('os_type').value = deviceInfo.osType;
            document.getElementById('os_version').value = deviceInfo.osVersion;
            document.getElementById('device_identifier').value = deviceInfo.identifier;
        }
    });
</script>
@endpush
