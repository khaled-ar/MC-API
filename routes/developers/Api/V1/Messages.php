<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MessageController;

Route::get( '/messages', [ MessageController::class, 'index' ] );
Route::get( '/messages/{message}', [ MessageController::class, 'show' ] );
Route::post( '/messages', [ MessageController::class, 'store' ] );
Route::delete( '/messages/{id}', [ MessageController::class, 'destroy' ] );

