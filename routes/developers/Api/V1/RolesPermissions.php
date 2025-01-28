<?php

use App\Http\Controllers\Api\V1\RoleController;
use Illuminate\Support\Facades\Route;

Route::get( 'roles', [ RoleController::class, 'index' ] );
Route::get( 'roles/{role}', [ RoleController::class, 'show' ] );
Route::put( 'roles/{role}', [ RoleController::class, 'update' ] );
