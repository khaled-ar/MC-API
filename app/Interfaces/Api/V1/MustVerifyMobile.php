<?php

namespace App\Interfaces\Api\V1;

interface MustVerifyMobile
{
    public function hasVerifiedMobile();

    public function markMobileAsVerified();

    public function sendMobileVerificationNotification();

    public function getMobileForVerification();
}
