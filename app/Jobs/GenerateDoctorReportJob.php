<?php

namespace App\Jobs;

use App\Models\AiReport;
use App\Models\User;
use App\Services\OpenAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateDoctorReportJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reportId,
        public int $doctorId
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
            
            $doctor = User::findOrFail($this->doctorId);
            
            // Collect aggregate patient data
            $patients = $doctor->patients()->with('sessions.days')->get();
            
            $totalPatients = $patients->count();
            $activeSessions = $doctor->sessions()->where('status', 'active')->count();
            $attentionFlagsCount = 0;
            $distribution = [];
            
            foreach ($patients as $patient) {
                $sessionCount = $patient->sessions()->count();
                $distribution[] = [
                    'patient_id' => $patient->id,
                    'session_count' => $sessionCount,
                ];
                
                // Count attention flags from session days
                foreach ($patient->sessions as $session) {
                    foreach ($session->days as $day) {
                        if ($day->alerts) {
                            $alerts = strtolower($day->alerts);
                            if (strpos($alerts, 'self-harm') !== false || 
                                strpos($alerts, 'panic') !== false || 
                                strpos($alerts, 'relapse') !== false) {
                                $attentionFlagsCount++;
                            }
                        }
                    }
                }
            }
            
            $patientData = [
                'total_patients' => $totalPatients,
                'active_sessions' => $activeSessions,
                'attention_flags_count' => $attentionFlagsCount,
                'distribution' => $distribution,
            ];
            
            // Generate report
            $result = $openAIService->generateDoctorReport($patientData);
            
            // Calculate snapshot hash
            $snapshotHash = md5(json_encode($patientData));
            
            // Update report
            $report->update([
                'status' => 'completed',
                'result_summary' => $result['summary'],
                'result_json' => $result['json'],
                'input_snapshot_hash' => $snapshotHash,
                'error_message' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('GenerateDoctorReportJob failed: ' . $e->getMessage());
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
