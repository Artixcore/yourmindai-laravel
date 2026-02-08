<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Session Report - {{ $report->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        h2 { font-size: 14px; margin-top: 16px; margin-bottom: 6px; }
        .meta { color: #666; font-size: 11px; margin-bottom: 16px; }
        .section { margin-bottom: 12px; }
        .section p { margin: 4px 0; }
        .label { font-weight: bold; color: #555; }
    </style>
</head>
<body>
    <h1>{{ $report->title }}</h1>
    <div class="meta">
        Patient: {{ $report->patient->user->name ?? $report->patient->full_name ?? 'N/A' }} |
        Created: {{ $report->created_at->format('M d, Y H:i') }} |
        By: {{ $report->createdByDoctor->name ?? 'N/A' }}
    </div>

    @if($report->summary)
    <div class="section">
        <h2>Summary</h2>
        <p>{{ $report->summary }}</p>
    </div>
    @endif

    @if($report->assessments_summary)
    <div class="section">
        <h2>Assessments Summary</h2>
        <p>{{ $report->assessments_summary }}</p>
    </div>
    @endif

    @if($report->techniques_assigned)
    <div class="section">
        <h2>Techniques Assigned</h2>
        <p>{{ $report->techniques_assigned }}</p>
    </div>
    @endif

    @if($report->progress_notes)
    <div class="section">
        <h2>Progress Notes</h2>
        <p>{{ $report->progress_notes }}</p>
    </div>
    @endif

    @if($report->next_steps)
    <div class="section">
        <h2>Next Steps</h2>
        <p>{{ $report->next_steps }}</p>
    </div>
    @endif

    <div class="meta" style="margin-top: 24px;">
        Generated on {{ now()->format('M d, Y H:i') }} — Your Mind Aid
    </div>
</body>
</html>
