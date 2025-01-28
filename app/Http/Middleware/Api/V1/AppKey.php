<?php

namespace App\Http\Middleware\Api\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppKey {
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( Request $request, Closure $next ): Response {
        if ( $request->header( 'app_key' ) !== env( 'APP_KEY' ) ) {
            return response()->json( [ 'app_key_status' => 0 ] );
        }
        return $next( $request );
    }
}
