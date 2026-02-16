<?php

namespace App\Http\Controllers;

use App\Models\SessionReport;
use App\Services\SessionReportPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportDownloadController extends Controller
{
    /**
     * Download session report PDF via signed URL (no auth required).
     */
    public function download(Request $request, $report)
    {
        $report = SessionReport::findOrFail($report);

        $service = app(SessionReportPdfService::class);
        if (!$report->pdf_path || !Storage::disk('public')->exists($report->pdf_path)) {
            try {
                $service->generateAndStore($report);
                $report->refresh();
            } catch (\Exception $e) {
                Log::error('Session report PDF generation failed', [
                    'report_id' => $report->id,
                    'route' => 'report.download-public',
                    'error' => $e->getMessage(),
                ]);
                abort(500, 'Unable to generate report PDF. Please try again later.');
            }
        }

        if (!$report->pdf_path || !Storage::disk('public')->exists($report->pdf_path)) {
            abort(404, 'Report PDF not found.');
        }

        $filename = 'session-report-' . $report->id . '-' . preg_replace('/[^a-zA-Z0-9\-_.]/', '-', $report->title ?? 'report') . '.pdf';

        return Storage::disk('public')->download($report->pdf_path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
