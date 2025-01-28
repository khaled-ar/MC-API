<?php

use App\Http\Controllers\Api\V1\ObjectionController;
use Illuminate\Support\Facades\Route;

Route::post( 'rating/objection', [ ObjectionController::class, 'store' ] );
Route::get( 'rating/objection/{id}', [ ObjectionController::class, 'show' ] );
Route::get( 'rating/objection', [ ObjectionController::class, 'index' ] );
Route::delete( 'rating/objection/{id}', [ ObjectionController::class, 'destroy' ] );
