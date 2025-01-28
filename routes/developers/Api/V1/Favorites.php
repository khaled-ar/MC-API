<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FollowUserController;
use App\Http\Controllers\Api\V1\FavoriteController;

Route::post( '/favorites/{ad_id}', [ FavoriteController::class, 'store' ] );
Route::delete( '/favorites/{fav_id}', [ FavoriteController::class, 'delete' ] );
Route::get( '/favorites', [ FavoriteController::class, 'index' ] );
