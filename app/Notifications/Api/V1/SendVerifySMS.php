<?php

namespace App\Notifications\Api\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class SendVerifySMS extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via()
    {
        return [TwilioChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toTwilio($notifiable)
    {
        $code = $this->generateCode($notifiable);
        return (new TwilioSmsMessage())
            ->content(' رمز التأكيد هو' . $code . ' نعلمكم أنه صالح لمدة ساعة واحدة فقط  ');
    }

    public function generateCode($notifiable)
    {
        $code = mt_rand(100000, 999999);
        Cache::put(request()->ip(), [$code, $notifiable->phone_number], now()->addHour());
        return $code;
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
