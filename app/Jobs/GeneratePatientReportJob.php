<?php

namespace App\Jobs;

use App\Models\AiReport;
use App\Models\Patient;
use App\Services\OpenAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GeneratePatientReportJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reportId,
        public int $patientId,
        public int $days = 30
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
            
            $patient = Patient::findOrFail($this->patientId);
            
            // Collect session data
            $sessionData = [];
            $sessions = $patient->sessions()
                ->where('created_at', '>=', now()->subDays($this->days))
                ->with('days')
                ->get();
            
            foreach ($sessions as $session) {
                foreach ($session->days as $day) {
                    $sessionData[] = [
                        'title' => $session->title,
                        'date' => $day->day_date->format('Y-m-d'),
                        'symptoms' => $day->symptoms,
                        'alerts' => $day->alerts,
                        'tasks' => $day->tasks,
                    ];
                }
            }
            
            if (empty($sessionData)) {
                throw new \Exception('No session data available for the specified period.');
            }
            
            // Generate report
            $result = $openAIService->generatePatientReport($sessionData, $this->days);
            
            // Calculate snapshot hash
            $snapshotHash = md5(json_encode($sessionData));
            
            // Update report
            $report->update([
                'status' => 'completed',
                'result_summary' => $result['summary'],
                'result_json' => $result['json'],
                'input_snapshot_hash' => $snapshotHash,
                'error_message' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('GeneratePatientReportJob failed: ' . $e->getMessage());
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
