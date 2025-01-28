<?php

use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get( 'users', [ UserController::class, 'index' ] );
Route::get( 'users/blocked', [ UserController::class, 'blocked' ] );
Route::get( 'users/pending-delete', [ UserController::class, 'pendingDelete' ] );
Route::get( 'users/results', [ UserController::class, 'results' ] );
Route::get( 'users/subscriptions', [ UserController::class, 'subscriptions' ] );
Route::get( 'users/{id}', [ UserController::class, 'show' ] );
Route::get( 'users/profile/{id}', [ UserController::class, 'profile' ] );
Route::post( 'users', [ UserController::class, 'store' ] );
Route::post( 'users/{id}', [ UserController::class, 'update' ] );
Route::delete( 'users/{id}', [ UserController::class, 'delete' ] );
Route::delete( 'users/force-delete/{id}', [ UserController::class, 'destroy' ] );
Route::post( 'users/restore/{id}', [ UserController::class, 'restore' ] );
Route::post( 'users/block/{id}', [ UserController::class, 'block' ] );
Route::post( 'users/unblock/{id}', [ UserController::class, 'unBlock' ] );
