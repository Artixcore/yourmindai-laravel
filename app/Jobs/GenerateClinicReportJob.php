<?php

namespace App\Jobs;

use App\Models\AiReport;
use App\Models\Patient;
use App\Models\Session;
use App\Models\User;
use App\Services\OpenAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateClinicReportJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reportId,
        public string $dateFrom,
        public string $dateTo
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAIService $openAIService): void
    {
        $report = AiReport::findOrFail($this->reportId);
        
        try {
            $report->update(['status' => 'running']);
            
            // Collect anonymized aggregate data only
            $totalDoctors = User::where('role', 'doctor')->count();
            $totalPatients = Patient::count();
            $totalSessions = Session::count();
            $activeSessions = Session::where('status', 'active')->count();
            $closedSessions = Session::where('status', 'closed')->count();
            
            $sessionsCreated = Session::whereBetween('created_at', [$this->dateFrom, $this->dateTo])->count();
            $resourcesPosted = \App\Models\PatientResource::whereBetween('created_at', [$this->dateFrom, $this->dateTo])->count();
            
            // Count attention flags (anonymized)
            $attentionFlags = 0;
            $sessionDays = \App\Models\SessionDay::whereHas('session', function ($q) {
                $q->whereBetween('created_at', [$this->dateFrom, $this->dateTo]);
            })->get();
            
            foreach ($sessionDays as $day) {
                if ($day->alerts) {
                    $alerts = strtolower($day->alerts);
                    if (strpos($alerts, 'self-harm') !== false || 
                        strpos($alerts, 'panic') !== false || 
                        strpos($alerts, 'relapse') !== false) {
                        $attentionFlags++;
                    }
                }
            }
            
            // Trends (anonymized)
            $trends = [
                'sessions_per_week' => $sessionsCreated / max(1, (strtotime($this->dateTo) - strtotime($this->dateFrom)) / 604800),
                'resources_per_week' => $resourcesPosted / max(1, (strtotime($this->dateTo) - strtotime($this->dateFrom)) / 604800),
            ];
            
            $aggregateData = [
                'total_doctors' => $totalDoctors,
                'total_patients' => $totalPatients,
                'total_sessions' => $totalSessions,
                'active_sessions' => $activeSessions,
                'closed_sessions' => $closedSessions,
                'sessions_created' => $sessionsCreated,
                'resources_posted' => $resourcesPosted,
                'trends' => $trends,
                'attention_flags' => $attentionFlags,
            ];
            
            // Generate report
            $result = $openAIService->generateClinicReport($aggregateData, $this->dateFrom, $this->dateTo);
            
            // Calculate snapshot hash
            $snapshotHash = md5(json_encode($aggregateData));
            
            // Update report
            $report->update([
                'status' => 'completed',
                'result_summary' => $result['summary'],
                'result_json' => $result['json'],
                'input_snapshot_hash' => $snapshotHash,
                'error_message' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('GenerateClinicReportJob failed: ' . $e->getMessage());
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
