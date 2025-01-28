<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\NotificationController;

Route::prefix('notifications')->controller(NotificationController::class)->group(function() {

        Route::get('/get-all', 'index');
        Route::get('/get-read', 'read');
        Route::get('/get-unread', 'unRead');
        Route::get('/trashed', 'trashed');
        Route::get('/show/{notification_id}', 'show');
        Route::delete('/delete/{notification_id}', 'delete');
        Route::delete('/force-delete/{notification_id}', 'forceDelete');
        Route::put('/restore/{notification_id}', 'restore');
        Route::put('/mark-as-read/{id}', 'markAsRead');
        Route::put('/mark-as-unread/{id}', 'markAsUnRead');

        Route::delete('/delete', 'MultipleDelete');
        Route::delete('/force-delete', 'MultipleForceDelete');
        Route::put('/mark-as-read',  'MultipleMarkAsRead');
        Route::put('/mark-as-unread',  'MultipleMarkAsUnRead');
        Route::put('/restore',  'MultipleRestore');
});
