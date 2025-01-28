<?php

use App\Http\Controllers\Api\V1\CommentController;
use Illuminate\Support\Facades\Route;

Route::get( '/comments', [ CommentController::class, 'index' ] );
Route::get( '/comments/{id}', [ CommentController::class, 'show' ] );
Route::post( '/comments', [ CommentController::class, 'store' ] );
Route::put( '/comments/{id}', [ CommentController::class, 'update' ] );
Route::delete( '/comments/destroy/{id}', [ CommentController::class, 'destroy' ] );
Route::delete( '/comments/unaccept/{id}', [ CommentController::class, 'unaccept' ] );
Route::put( '/comments/accept/{id}', [ CommentController::class, 'accept' ] );
