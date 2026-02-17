<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $title;
    protected $message;
    protected $url;

    public function __construct(Appointment $appointment, string $title, string $message, string $url)
    {
        $this->appointment = $appointment;
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
            'type' => 'new_appointment',
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->appointment->doctor_id,
            'patient_id' => $this->appointment->patient_id,
        ];
    }
}
