<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\MobileVerificationRequest;
use Illuminate\Auth\Events\Verified;

class VerifyMobileController extends Controller
{

    public function __invoke(MobileVerificationRequest $request)
    {
        // mark user's phone as verified
        if ($request->user()->markMobileAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json([
            'status' => 1,
            'message' => 'تم تأكيد رقمكم بنجاح'
        ]);
    }
}
