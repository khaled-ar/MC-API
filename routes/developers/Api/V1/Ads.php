<?php

use App\Http\Controllers\Api\V1\AdController;
use Illuminate\Support\Facades\Route;

Route::put( 'ads/accept/{id}', [ AdController::class, 'accept' ] );
Route::put( 'ads/unaccept/{id}', [ AdController::class, 'unaccept' ] );
Route::put( 'ads/re-post/{id}', [ AdController::class, 'rePost' ] );
Route::get( 'ads/only-pending', [ AdController::class, 'onlyPending' ] );
Route::post( 'ads/{id}', [ AdController::class, 'update' ] );

Route::apiResource( 'ads', AdController::class );
