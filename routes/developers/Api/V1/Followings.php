<?php

use App\Http\Controllers\Api\V1\FollowUserController;
use Illuminate\Support\Facades\Route;

Route::post( 'follow-user/{id}', [ FollowUserController::class, 'followUser' ] );
Route::delete( 'unfollow-user/{id}', [ FollowUserController::class, 'unFollowUser' ] );
Route::get( 'get-followers', [ FollowUserController::class, 'getFollowers' ] );
