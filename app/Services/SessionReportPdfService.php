<?php

namespace App\Services;

use App\Models\SessionReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SessionReportPdfService
{
    /**
     * Generate PDF for a session report and store in system. Returns the stored path or null on failure.
     */
    public function generateAndStore(SessionReport $report): ?string
    {
        $report->load(['patient.user', 'session', 'createdByDoctor']);

        $pdf = Pdf::loadView('pdf.session-report', compact('report'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'session-report-' . $report->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        $path = 'session-reports/' . $filename;

        $content = $pdf->output();
        if (Storage::disk('public')->put($path, $content) === false) {
            return null;
        }

        $report->update(['pdf_path' => $path]);

        return $path;
    }

    /**
     * Get full path for storage.
     */
    public function getStoredPath(SessionReport $report): ?string
    {
        if (!$report->pdf_path) {
            return null;
        }
        return Storage::disk('public')->path($report->pdf_path);
    }
}
