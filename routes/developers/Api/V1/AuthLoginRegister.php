<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;

Route::post( '/login', [ LoginController::class, 'login' ] );
Route::post( '/register', [ UserController::class, 'register' ] );
