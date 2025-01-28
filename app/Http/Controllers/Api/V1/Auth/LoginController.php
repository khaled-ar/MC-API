<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\Api\V1\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;

class LoginController extends Controller {
    public function login( LoginRequest $request ) {

        if ( $request->authenticate() ) {

            $user = User::where( 'phone_number', $request->phone_number )->first();
            $user->tokens()->delete();

            $role = $user->is_admin ? $user->admin->role->name : 'user';
            unset( $user[ 'admin' ] );

            $token = $user->createToken(
                'remember_token',
                ['auth:remember'],
                now()->addMonth()
            )->plainTextToken;

            return response()->json( [
                'status' => 1,
                'message' => 'مرحباً بك مجدداً يا ' . $user->username .'، إبدأ البيع والشراء الان',
                'user' => $user,
                'role' => $role,
                'token' => $token,
            ] );

        } else {
            return response()->json( [
                'status' => 0,
                'message' => 'بيانات تسجيل الدخول غير صحيحة'
            ] );
        }
    }
}
