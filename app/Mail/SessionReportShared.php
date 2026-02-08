<?php

namespace App\Mail;

use App\Models\SessionReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SessionReportShared extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SessionReport $report,
        public string $downloadLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Session Report: ' . $this->report->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.session-report-shared',
        );
    }
}
