<?php

namespace App\Traits\Api\V1;

use App\Notifications\Api\V1\SendVerifySMS;

trait MustVerifyMobile
{
    /**
     * Determine if the user has verified their phone number.
     *
     * @return bool
     */
    public function hasVerifiedMobile(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Mark the given user's phone number as verified.
     *
     * @return bool
     */
    public function markMobileAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the mobile verification notification.
     *
     * @return void
     */
    public function sendMobileVerificationNotification(): void
    {
        $this->notify(new SendVerifySMS());
    }

    /**
     * Get the phone number that should be used for verification.
     *
     * @return string
     */
    public function getMobileForVerification()
    {
        return $this->phone_number;
    }
}
