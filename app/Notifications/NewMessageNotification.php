<?php

namespace App\Notifications;

use App\Models\PatientMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $patientMessage;
    protected $title;
    protected $messageText;
    protected $url;
    protected $senderId;
    protected $senderType;

    public function __construct(PatientMessage $patientMessage, string $title, string $messageText, string $url, $senderId = null, $senderType = null)
    {
        $this->patientMessage = $patientMessage;
        $this->title = $title;
        $this->messageText = $messageText;
        $this->url = $url;
        $this->senderId = $senderId;
        $this->senderType = $senderType;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_message',
            'title' => $this->title,
            'message' => $this->messageText,
            'url' => $this->url,
            'message_id' => $this->patientMessage->id,
            'sender_id' => $this->senderId,
            'sender_type' => $this->senderType,
        ];
    }
}
