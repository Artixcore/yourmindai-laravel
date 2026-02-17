<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Session;
use App\Models\ContactMessage;
use App\Models\AppointmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('admin_dashboard_stats', 60, function () {
            return [
                'total_doctors' => User::where('role', 'doctor')->where('status', 'active')->count(),
                'total_patients' => Patient::where('status', 'active')->count(),
                'active_sessions' => Session::where('status', 'active')->count(),
                'pending_messages' => ContactMessage::where('status', 'new')->count(),
                'pending_requests' => AppointmentRequest::where('status', 'pending')->count(),
            ];
        });

        return view('admin.dashboard.index', compact('stats'));
    }
}
