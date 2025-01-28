<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogoutController extends Controller {
    public function logout( Request $request ) {

        if ( $request->user()->tokens()->delete() ) {
            return response()->json( [
                'status' => 1,
            ] );
        }

        return response()->json( [
            'status' => 0,
        ] );
    }
}
