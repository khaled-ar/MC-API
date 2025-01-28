<?php

namespace App\Http\Middleware\Api\V1;

use App\Interfaces\Api\V1\MustVerifyMobile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileIsVerifiedMiddleware {
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( Request $request, Closure $next ): Response {
        if ( $request->user() instanceof MustVerifyMobile && !$request->user()->hasVerifiedMobile() && !$request->user()->is_admin ) {
            return response()->json( [ 'status' => 0, 'message' => 'لا يمكن القيام بهذا الإجراء لأن رقم هاتفك غير مؤكد' ] );
        }
        return $next( $request );
    }
}
