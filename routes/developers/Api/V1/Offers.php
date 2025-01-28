<?php

use App\Http\Controllers\Api\V1\OfferController;
use Illuminate\Support\Facades\Route;

Route::get( 'offers', [ OfferController::class, 'index' ] );
Route::get( 'offers/{id}', [ OfferController::class, 'show' ] );
Route::post( 'offers', [ OfferController::class, 'store' ] );
Route::patch( 'offers/{id}', [ OfferController::class, 'update' ] );
Route::delete( 'offers/{id}', [ OfferController::class, 'destroy' ] );
Route::put( 'offers/accept/{id}', [ OfferController::class, 'accept' ] );
Route::put( 'offers/unaccept/{id}', [ OfferController::class, 'unaccept' ] );
