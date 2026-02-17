<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PatientTransferredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $patientId;
    protected $fromDoctorId;
    protected $toDoctorId;
    protected $title;
    protected $message;
    protected $url;

    public function __construct($patientId, $fromDoctorId, $toDoctorId, $title, $message, $url)
    {
        $this->patientId = $patientId;
        $this->fromDoctorId = $fromDoctorId;
        $this->toDoctorId = $toDoctorId;
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'patient_transferred',
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'patient_id' => $this->patientId,
            'from_doctor_id' => $this->fromDoctorId,
            'to_doctor_id' => $this->toDoctorId,
        ];
    }
}
