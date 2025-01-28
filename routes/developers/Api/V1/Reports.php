<?php

use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::get( '/reports', [ ReportController::class, 'index' ] );
Route::post( '/reports', [ ReportController::class, 'store' ] );
Route::get( '/reports/{id}', [ ReportController::class, 'show' ] );
Route::delete( '/reports/{id}', [ ReportController::class, 'destroy' ] );
