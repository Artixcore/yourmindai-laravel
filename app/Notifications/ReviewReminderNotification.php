<?php

namespace App\Notifications;

use App\Models\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $type;
    protected $session;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $type, ?Session $session = null)
    {
        $this->type = $type;
        $this->session = $session;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('We\'d love your feedback!');

        if ($this->type === 'doctor') {
            $message->line('Your recent sessions have been completed.')
                    ->line('We would greatly appreciate if you could take a moment to review your experience with your doctor.')
                    ->action('Leave a Review', url('/client/reviews/create?type=doctor'))
                    ->line('Your feedback helps us improve our services.');
        } else if ($this->type === 'session' && $this->session) {
            $message->line('Your therapy session "' . $this->session->title . '" has been completed.')
                    ->line('Please take a moment to share your feedback about this session.')
                    ->action('Review Session', url('/client/reviews/create?type=session&session_id=' . $this->session->id))
                    ->line('Your input is valuable and helps improve your care.');
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => $this->type,
            'session_id' => $this->session ? $this->session->id : null,
            'session_title' => $this->session ? $this->session->title : null,
            'message' => $this->type === 'doctor' 
                ? 'Please review your experience with your doctor' 
                : 'Please review your recent therapy session',
        ];
    }
}
