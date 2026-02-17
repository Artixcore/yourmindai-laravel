<?php

namespace App\Notifications;

use App\Models\HomeworkAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class HomeworkAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $homework;
    protected $title;
    protected $message;
    protected $url;

    public function __construct(HomeworkAssignment $homework, string $title, string $message, string $url)
    {
        $this->homework = $homework;
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
            'type' => 'homework_assigned',
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'homework_id' => $this->homework->id ?? null,
            'patient_id' => $this->homework->patient_id ?? null,
        ];
    }
}
