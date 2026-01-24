<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\PatientProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['patient.user', 'assignedByDoctor']);

        // Apply filters
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('doctor_id')) {
            $query->where('assigned_by_doctor_id', $request->doctor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('due_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('due_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->orderBy('due_date', 'desc')->paginate(20);

        // Get doctors for filter dropdown
        $doctors = User::where('role', 'doctor')->get();

        // Get patients for filter dropdown
        $patients = PatientProfile::with('user')->get();

        // Calculate statistics
        $stats = [
            'total' => Task::count(),
            'by_status' => Task::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'overdue' => Task::where('due_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
            'completed_this_week' => Task::where('status', 'completed')
                ->where('completed_at', '>=', Carbon::now()->startOfWeek())
                ->count(),
            'avg_points' => round(Task::avg('points'), 2),
        ];

        return view('admin.tasks.index', compact('tasks', 'doctors', 'patients', 'stats'));
    }

    public function show($id)
    {
        $task = Task::with(['patient.user', 'assignedByDoctor'])->findOrFail($id);

        return view('admin.tasks.show', compact('task'));
    }

    public function analytics(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Overall statistics
        $totalTasks = Task::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedTasks = Task::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        // Tasks by doctor
        $tasksByDoctor = Task::whereBetween('created_at', [$startDate, $endDate])
            ->select('assigned_by_doctor_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'))
            ->with('assignedByDoctor')
            ->groupBy('assigned_by_doctor_id')
            ->orderBy('total', 'desc')
            ->get();

        // Task completion trends
        $weeklyTrends = Task::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            )
            ->groupBy('year', 'week')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();

        // Top performing patients (highest completion rate)
        $topPatients = Task::whereBetween('created_at', [$startDate, $endDate])
            ->select('patient_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'))
            ->with('patient.user')
            ->groupBy('patient_id')
            ->havingRaw('total >= 3') // At least 3 tasks
            ->get()
            ->map(function($item) {
                $item->completion_rate = $item->total > 0 ? round(($item->completed / $item->total) * 100, 2) : 0;
                return $item;
            })
            ->sortByDesc('completion_rate')
            ->take(10);

        return view('admin.tasks.analytics', compact(
            'totalTasks',
            'completedTasks',
            'completionRate',
            'tasksByDoctor',
            'weeklyTrends',
            'topPatients',
            'startDate',
            'endDate'
        ));
    }
}
