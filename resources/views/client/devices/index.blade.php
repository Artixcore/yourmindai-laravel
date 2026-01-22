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
<script>
(function() {
    'use strict';
    
    /**
     * Device Detection and Registration
     * Auto-detects device information for client device registration
     */
    
    /**
     * Generate a unique device identifier (UUID)
     */
    function generateDeviceIdentifier() {
        try {
            // Check if we already have a device ID in localStorage
            let deviceId = localStorage.getItem('mindAidDeviceId');
            
            if (!deviceId) {
                // Generate a new UUID v4
                deviceId = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    const r = Math.random() * 16 | 0;
                    const v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
                
                // Store in localStorage
                localStorage.setItem('mindAidDeviceId', deviceId);
            }
            
            return deviceId;
        } catch (e) {
            // Fallback if localStorage is not available
            return 'device-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        }
    }
    
    /**
     * Detect device type (mobile, tablet, desktop)
     */
    function detectDeviceType() {
        try {
            const width = window.innerWidth;
            const userAgent = navigator.userAgent.toLowerCase();
            
            // Check for mobile devices
            if (/mobile|android|iphone|ipod|blackberry|iemobile|opera mini/i.test(userAgent)) {
                return 'mobile';
            }
            
            // Check for tablets
            if (/tablet|ipad|playbook|silk/i.test(userAgent) || (width >= 600 && width < 1024)) {
                return 'tablet';
            }
            
            // Default to desktop
            return 'desktop';
        } catch (e) {
            return 'desktop';
        }
    }
    
    /**
     * Detect operating system
     */
    function detectOS() {
        try {
            const userAgent = navigator.userAgent;
            const platform = navigator.platform.toLowerCase();
            
            if (/iphone|ipad|ipod/i.test(userAgent)) {
                return 'iOS';
            }
            
            if (/android/i.test(userAgent)) {
                return 'Android';
            }
            
            if (/win/i.test(platform)) {
                return 'Windows';
            }
            
            if (/mac/i.test(platform)) {
                return 'macOS';
            }
            
            if (/linux/i.test(platform)) {
                return 'Linux';
            }
            
            return 'Unknown';
        } catch (e) {
            return 'Unknown';
        }
    }
    
    /**
     * Detect OS version
     */
    function detectOSVersion() {
        try {
            const userAgent = navigator.userAgent;
            
            // iOS version
            const iosMatch = userAgent.match(/OS (\d+)_(\d+)_?(\d+)?/);
            if (iosMatch) {
                return iosMatch[1] + '.' + iosMatch[2] + (iosMatch[3] ? '.' + iosMatch[3] : '');
            }
            
            // Android version
            const androidMatch = userAgent.match(/Android (\d+(\.\d+)?)/);
            if (androidMatch) {
                return androidMatch[1];
            }
            
            // Windows version
            const windowsMatch = userAgent.match(/Windows NT (\d+\.\d+)/);
            if (windowsMatch) {
                return windowsMatch[1];
            }
            
            return null;
        } catch (e) {
            return null;
        }
    }
    
    /**
     * Detect browser name
     */
    function detectBrowser() {
        try {
            const userAgent = navigator.userAgent;
            
            if (userAgent.indexOf('Chrome') > -1 && userAgent.indexOf('Edg') === -1) {
                return 'Chrome';
            }
            
            if (userAgent.indexOf('Safari') > -1 && userAgent.indexOf('Chrome') === -1) {
                return 'Safari';
            }
            
            if (userAgent.indexOf('Firefox') > -1) {
                return 'Firefox';
            }
            
            if (userAgent.indexOf('Edg') > -1) {
                return 'Edge';
            }
            
            return 'Unknown';
        } catch (e) {
            return 'Unknown';
        }
    }
    
    /**
     * Generate device name based on detected information
     */
    function generateDeviceName() {
        try {
            const deviceType = detectDeviceType();
            const os = detectOS();
            const browser = detectBrowser();
            
            return `${os} ${deviceType.charAt(0).toUpperCase() + deviceType.slice(1)} (${browser})`;
        } catch (e) {
            return 'Unknown Device';
        }
    }
    
    /**
     * Main function to detect all device information
     */
    function detectDeviceInfo() {
        try {
            return {
                identifier: generateDeviceIdentifier(),
                name: generateDeviceName(),
                type: detectDeviceType(),
                osType: detectOS(),
                osVersion: detectOSVersion(),
                browser: detectBrowser(),
                screenWidth: window.innerWidth || 0,
                screenHeight: window.innerHeight || 0,
            };
        } catch (e) {
            return {
                identifier: generateDeviceIdentifier(),
                name: 'Unknown Device',
                type: 'desktop',
                osType: 'Unknown',
                osVersion: null,
                browser: 'Unknown',
                screenWidth: 0,
                screenHeight: 0,
            };
        }
    }
    
    // Auto-detect device info when modal opens
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('registerDeviceModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', function() {
                try {
                    const deviceInfo = detectDeviceInfo();
                    const nameField = document.getElementById('device_name');
                    const typeField = document.getElementById('device_type');
                    const osTypeField = document.getElementById('os_type');
                    const osVersionField = document.getElementById('os_version');
                    const identifierField = document.getElementById('device_identifier');
                    
                    if (nameField) nameField.value = deviceInfo.name || '';
                    if (typeField) typeField.value = deviceInfo.type || 'mobile';
                    if (osTypeField) osTypeField.value = deviceInfo.osType || '';
                    if (osVersionField) osVersionField.value = deviceInfo.osVersion || '';
                    if (identifierField) identifierField.value = deviceInfo.identifier || '';
                } catch (e) {
                    // Silently fail - user can manually enter device info
                }
            });
        }
    });
})();
</script>
@endpush
