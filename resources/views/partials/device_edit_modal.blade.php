<div class="modal fade" id="editDeviceModal{{ $device->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('client.devices.update', $device) }}" class="ajax-form" data-target="#device-card-{{ $device->id }}" data-replace="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Device Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="device_name" value="{{ old('device_name', $device->device_name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Device Type</label>
                        <select class="form-select" name="device_type" required>
                            @foreach(['wearable','smartwatch','mobile','tablet','desktop','other'] as $t)
                            <option value="{{ $t }}" {{ $device->device_type === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Identifier (optional)</label>
                        <input type="text" class="form-control" name="device_identifier" value="{{ old('device_identifier', $device->device_identifier) }}" placeholder="Serial number or model ID">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2">{{ old('notes', $device->notes) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Saving...">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
