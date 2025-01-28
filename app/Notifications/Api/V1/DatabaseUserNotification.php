<?php

namespace App\Notifications\Api\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DatabaseUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private string $message,
        private string $from,
        private string $subject
    ) {}


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        return [
            'from' => $this->from,
            'to' => $notifiable->fullname,
            'subject' => $this->subject,
            'body' => $this->message,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
