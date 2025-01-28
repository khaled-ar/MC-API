<?php

use App\Rules\Api\V1\oldPasswordValidate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\NewPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;

Route::post( '/forgot-password', ForgotPasswordController::class );
Route::post( '/reset-password', NewPasswordController::class );

/* Route::post( '/check-code', function(Request $request) {

    $request->validate([
        'code' => ['required', 'integer']
    ]);

    if(Cache::get($request->ip())[0] != $request->code) {
        return response()->json([
            'status' => 0,
            'message' => 'الرمز المدخل غير صحيح'
        ]);
    }
    return response()->json([
        'status'=> 1,
    ]);

}); */

Route::post('/change-password', function(Request $request) {

    $request->validate([
        'current_password' => ['required', new oldPasswordValidate($request->user())],
        'password' => ['required', 'string', Password::min( 8 )->letters()->numbers(), 'confirmed']
    ]);

    $new_password = $request->user()->update(['password' => $request->password]);

    if($new_password) {
        return response()->json([
            'status'=> 1,
            'message'=> 'تم تغيير كلمة المرور بنجاح'
        ]);
    }

    return response()->json([
        'status'=> 0,
        'message'=> 'عذرا، يوجد خطأ ما'
    ]);

})->middleware('auth:sanctum');
