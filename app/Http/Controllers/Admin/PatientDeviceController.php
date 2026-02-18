<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientDevice;
use App\Models\PatientProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatientDeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = PatientDevice::with('patientProfile.user');

        // Apply filters
        if ($request->filled('patient_id')) {
            $query->where('patient_profile_id', $request->patient_id);
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('device_name', 'like', '%' . $request->search . '%')
                  ->orWhere('device_identifier', 'like', '%' . $request->search . '%');
            });
        }

        $devices = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get patients for filter dropdown
        $patients = PatientProfile::with('user')->get();

        // Get unique device types for filter
        $deviceTypes = PatientDevice::select('device_type')->distinct()->whereNotNull('device_type')->pluck('device_type');

        // Calculate statistics
        $stats = [
            'total' => PatientDevice::count(),
            'by_type' => PatientDevice::select('device_type', DB::raw('COUNT(*) as count'))
                ->groupBy('device_type')
                ->pluck('count', 'device_type')
                ->toArray(),
            'active_this_week' => PatientDevice::where('last_active_at', '>=', Carbon::now()->subWeek())->count(),
            'new_this_month' => PatientDevice::whereMonth('created_at', Carbon::now()->month)->count(),
            'patients_with_multiple' => $this->getPatientsWithMultipleDevices(),
        ];

        return view('admin.devices.index', compact('devices', 'patients', 'deviceTypes', 'stats'));
    }

    public function show($id)
    {
        $device = PatientDevice::with('patientProfile.user')->findOrFail($id);

        // Get device activity history (if tracked)
        $activityHistory = [
            'first_seen' => $device->created_at,
            'last_active' => $device->last_active_at,
            'total_days' => $device->created_at->diffInDays(now()),
        ];

        return view('admin.devices.show', compact('device', 'activityHistory'));
    }

    public function destroy($id)
    {
        $device = PatientDevice::findOrFail($id);
        $device->delete();

        return redirect()->route('admin.devices.index')
            ->with('success', 'Device removed successfully.');
    }

    public function analytics(Request $request)
    {
        // Date range filter
        $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Device registrations over time
        $registrationTrends = PatientDevice::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Platform distribution
        $platformDistribution = PatientDevice::select('platform', DB::raw('COUNT(*) as count'))
            ->groupBy('platform')
            ->get();

        // Device type distribution
        $deviceTypeDistribution = PatientDevice::select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->get();

        // Patients with multiple devices
        $multipleDevices = PatientDevice::select('patient_profile_id', DB::raw('COUNT(*) as device_count'))
            ->with('patientProfile.user')
            ->groupBy('patient_profile_id')
            ->having('device_count', '>', 1)
            ->orderBy('device_count', 'desc')
            ->limit(20)
            ->get();

        // Active vs inactive devices (last 30 days)
        $activeDevices = PatientDevice::where('last_active_at', '>=', Carbon::now()->subDays(30))->count();
        $inactiveDevices = PatientDevice::where('last_active_at', '<', Carbon::now()->subDays(30))
            ->orWhereNull('last_active_at')
            ->count();

        return view('admin.devices.analytics', compact(
            'registrationTrends',
            'platformDistribution',
            'deviceTypeDistribution',
            'multipleDevices',
            'activeDevices',
            'inactiveDevices',
            'startDate',
            'endDate'
        ));
    }

    private function getPatientsWithMultipleDevices()
    {
        return PatientDevice::select('patient_profile_id')
            ->groupBy('patient_profile_id')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->count();
    }
}
