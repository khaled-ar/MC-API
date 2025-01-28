<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\MobileVerificationNotificationController;
use App\Http\Controllers\Api\V1\Auth\VerifyMobileController;

Route::post( '/mobile/verification-notification', MobileVerificationNotificationController::class )
->middleware( [ 'throttle:1,2' ] )
->name( 'verification.send' );

Route::post( '/verify-mobile', VerifyMobileController::class )
->middleware( [ 'throttle:6,1' ] )
->name( 'verification.verify' );
