<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;
    protected $title;
    protected $message;
    protected $url;

    public function __construct(Task $task, string $title, string $message, string $url)
    {
        $this->task = $task;
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
            'type' => 'task_completed',
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'task_id' => $this->task->id,
            'patient_id' => $this->task->patient_id,
            'completed_by' => $this->task->patient_id,
        ];
    }
}
