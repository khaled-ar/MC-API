<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MobileVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function __invoke(Request $request): JsonResponse|RedirectResponse
    {

        //checking if user's phone is already verified
        if ($request->user()->hasVerifiedMobile()) {
            return response()->json(['message' => 'ُرقم هاتفكم قد تم تأكيده بالفعل']);
        }

        try {
            //sending verfication code
            $request->user()->sendMobileVerificationNotification();

        } catch (\Exception) {
            return response()->json([
                'status' => 0,
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'تم إرسال رمز التحقق إلى رقمكم'
        ]);
    }
}
