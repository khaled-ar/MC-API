<?php

namespace App\Traits\Api\V1;

trait PhoneUpdate
{
    public function phoneVerificationReset($user) {

        if($user->wasChanged('phone_number')) {
            $user->phone_verified_at = null;
            $user->save();
            $user->sendMobileVerificationNotification();
        }
    }
}
