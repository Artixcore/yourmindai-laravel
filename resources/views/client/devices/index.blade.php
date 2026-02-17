@extends('client.layout')

@section('title', 'My Devices - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">My Devices</h4>
    <p class="text-muted mb-0 small">Manage your registered devices</p>
</div>

<!-- Add Device Buttons -->
<div class="card mb-3">
    <div class="card-body text-center d-flex flex-wrap gap-2 justify-content-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerDeviceModal">
            <i class="bi bi-phone me-2"></i>
            Register App Device
        </button>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSmartDeviceModal">
            <i class="bi bi-smartwatch me-2"></i>
            Add Smart Device
        </button>
    </div>
</div>

<!-- Devices List -->
<div id="devices-empty-state" class="card" style="{{ $devices->isNotEmpty() ? 'display:none' : '' }}">
    <div class="card-body text-center py-5">
        <i class="bi bi-phone display-1 text-muted mb-3"></i>
        <p class="text-muted mb-0">No devices registered yet.</p>
        <p class="text-muted small">Register your first device to get started.</p>
    </div>
</div>
<div id="devices-list" style="{{ $devices->isEmpty() ? 'display:none' : '' }}">
@foreach($devices as $device)
@include('partials.device_card', ['device' => $device])
@include('partials.device_edit_modal', ['device' => $device])
@endforeach
</div>

<!-- Register Device Modal -->
<div class="modal fade" id="registerDeviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register New Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('client.devices.store') }}" class="ajax-form" data-target="#devices-list">
                @csrf
                <input type="hidden" name="device_source" value="app_registered">
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
                    <button type="submit" class="btn btn-primary" data-loading-text="Registering...">Register Device</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Smart Device Modal (manual entry) -->
<div class="modal fade" id="addSmartDeviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Smart Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('client.devices.store') }}" class="ajax-form" data-target="#devices-list">
                @csrf
                <input type="hidden" name="device_source" value="manual">
                <div class="modal-body">
                    <p class="text-muted small">Add a wearable, smartwatch, or other device manually.</p>
                    <div class="mb-3">
                        <label class="form-label">Device Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="device_name" required placeholder="e.g., Apple Watch, Fitbit">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Device Type</label>
                        <select class="form-select" name="device_type" required>
                            <option value="wearable">Wearable</option>
                            <option value="smartwatch">Smartwatch</option>
                            <option value="mobile">Mobile</option>
                            <option value="tablet">Tablet</option>
                            <option value="desktop">Desktop</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Identifier (optional)</label>
                        <input type="text" class="form-control" name="device_identifier" placeholder="Serial number or model ID">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Any additional info..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Adding...">Add Device</button>
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
