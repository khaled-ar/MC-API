<?php

use App\Http\Controllers\Api\V1\RatingController;
use Illuminate\Support\Facades\Route;

Route::apiResource( 'ratings', RatingController::class );
