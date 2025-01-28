<?php

use App\Http\Controllers\Api\V1\AdminController;
use Illuminate\Support\Facades\Route;

Route::get( 'admins', [ AdminController::class, 'index' ] );
Route::get( 'admins/{admin}', [ AdminController::class, 'show' ] );
Route::post( 'admins/{user}/{role}', [ AdminController::class, 'updateOrStore' ] );
Route::delete( 'admins/{user}', [ AdminController::class, 'delete' ] );
