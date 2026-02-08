<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Session Report</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <p>Hello,</p>
    <p>A session report has been shared with you: <strong>{{ $report->title }}</strong>.</p>
    <p>Download the report using the link below (valid for 7 days):</p>
    <p><a href="{{ $downloadLink }}" style="color: #0d6efd;">{{ $downloadLink }}</a></p>
    <p>Best regards,<br>{{ config('app.name') }}</p>
</body>
</html>
