<?php

namespace App\Listeners\Api\V1;

use App\Interfaces\Api\V1\MustVerifyMobile;
use Illuminate\Auth\Events\Registered;

class SendMobileVerificationNotification
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        if ($event->user instanceof MustVerifyMobile && ! $event->user->hasVerifiedMobile()) {
            $event->user->sendMobileVerificationNotification();
        }
    }
}
