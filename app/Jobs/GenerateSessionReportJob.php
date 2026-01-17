<?php

namespace App\Jobs;

use App\Models\AiReport;
use App\Models\Session;
use App\Services\OpenAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateSessionReportJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reportId,
        public int $sessionId
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
            
            $session = Session::with('days')->findOrFail($this->sessionId);
            
            // Collect session day data
            $sessionDayData = [
                'title' => $session->title,
                'symptoms' => '',
                'alerts' => '',
                'tasks' => '',
            ];
            
            foreach ($session->days as $day) {
                $sessionDayData['symptoms'] .= ($sessionDayData['symptoms'] ? "\n\n" : '') . $day->symptoms;
                $sessionDayData['alerts'] .= ($sessionDayData['alerts'] ? "\n\n" : '') . $day->alerts;
                $sessionDayData['tasks'] .= ($sessionDayData['tasks'] ? "\n\n" : '') . $day->tasks;
            }
            
            if (empty($sessionDayData['symptoms']) && empty($sessionDayData['alerts']) && empty($sessionDayData['tasks'])) {
                throw new \Exception('No session day data available.');
            }
            
            // Generate report
            $result = $openAIService->generateSessionReport($sessionDayData);
            
            // Calculate snapshot hash
            $snapshotHash = md5(json_encode($sessionDayData));
            
            // Update report
            $report->update([
                'status' => 'completed',
                'result_summary' => $result['summary'],
                'result_json' => $result['json'],
                'input_snapshot_hash' => $snapshotHash,
                'error_message' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('GenerateSessionReportJob failed: ' . $e->getMessage());
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
