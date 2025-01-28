<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\NewPasswordRequest;
use App\Models\Api\V1\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class NewPasswordController extends Controller {

    /**
    * Handle an incoming new password request.
    *
    * @throws \Illuminate\Validation\ValidationException
    */

    public function __invoke( NewPasswordRequest $request ): JsonResponse {

        $phone_number = Cache::get( $request->ip() )[ 1 ] ?? null;
        if(! $phone_number) {
            return response()->json([
                'status' => 0, 
                'message' => 'يرجى الذهاب لصفحة نسيت كلمة المرور'
            ]);
        }

        $user = User::firstWhere( 'phone_number', $phone_number );

        $user->update( $request->safe()->only( 'password' ) );

        return response()->json( [
            'status' => 1,
            'message' => 'تم تغيير كلمة السر بنجاح'
        ] );
    }
}
