<?php

use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Support\Facades\Route;

Route::delete( 'categories/delete/{id}', [ CategoryController::class, 'delete' ] );
Route::put( 'categories/restore/{id}', [ CategoryController::class, 'restore' ] );
Route::get( 'categories/only-trashed', [ CategoryController::class, 'onlyTrashed' ] );
Route::post( 'categories/{id}', [ CategoryController::class, 'update' ] );

Route::apiResource( 'categories', CategoryController::class, );

