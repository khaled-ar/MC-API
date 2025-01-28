<?php

use App\Http\Controllers\Api\V1\PackageController;
use Illuminate\Support\Facades\Route;

Route::put( 'restore-package/{id}', [ PackageController::class, 'restore' ] );
Route::delete( 'delete-package/{id}', [ PackageController::class, 'delete' ] );
Route::get( 'packages/trashed', [ PackageController::class, 'onlyTrashed' ] );
Route::apiResource( 'packages', PackageController::class );
