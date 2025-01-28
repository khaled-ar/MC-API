<?php

use App\Http\Controllers\Api\V1\HomeController;
use Illuminate\Support\Facades\Route;

Route::get( 'home/data', [ HomeController::class, 'homeData' ] );
Route::get( 'home/mobile/data', [ HomeController::class, 'homeMobileData' ] );
Route::get( 'home/show-ad/{id}', [ HomeController::class, 'showAd' ] );
Route::get( 'home/show-category/{id}', [ HomeController::class, 'showCategory' ] );
