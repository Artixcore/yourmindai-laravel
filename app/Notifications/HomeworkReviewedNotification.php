<?php

namespace App\Notifications;

use App\Models\HomeworkAssignment;
use App\Models\HomeworkCompletion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class HomeworkReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $homework;
    protected $completion;
    protected $title;
    protected $message;
    protected $url;

    public function __construct(HomeworkAssignment $homework, HomeworkCompletion $completion, string $title, string $message, string $url)
    {
        $this->homework = $homework;
        $this->completion = $completion;
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
            'type' => 'homework_reviewed',
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'homework_id' => $this->homework->id ?? null,
            'completion_id' => $this->completion->id ?? null,
        ];
    }
}
