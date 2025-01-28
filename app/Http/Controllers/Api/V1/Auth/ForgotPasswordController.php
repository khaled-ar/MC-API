<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\User;
// use App\Notifications\Api\V1\SendVerifySMS;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller {

    public function __invoke( Request $request ): JsonResponse {
        $request->validate( [
            'phone_number' => [ 'required', 'string' ],
            'username' => [ 'required', 'string' ]
        ] );

        $user = User::Where( 'phone_number', $request->phone_number )
        ->Where( 'username', $request->username )->first();

        if ( empty( $user ) ) {
            return response()->json( [
                'status' => 0,
                'message' => 'الرجاء التأكد من صحة البيانات المدخلة.'
            ] );
        }

        // $user->notify( new SendVerifySMS() );

        Cache::put($request->ip(), [null, $request->phone_number]);

        return response()->json( [
            'status' => 1,
            // 'message' => 'تم إرسال رمز التأكيد إلى رقمكم'
        ] );
    }
}
