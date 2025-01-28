<?php

use App\Http\Controllers\Api\V1\ResultController;
use Illuminate\Support\Facades\Route;

Route::prefix('results')->controller(ResultController::class)->group(function() {

    /* // return active ads
    Route::get('/get-active', 'getActive');

    // return updateable ads
    Route::get('/get-updateable', 'getUpdateable');

    // return inactive ads
    Route::get('/get-inactive', 'getInactive');

    // return almost inactive ads
    Route::get('/get-almost-inactive', 'getAlmostInactive');

    // return active offers ads
    Route::get('/get-active-offers', 'getActiveOffers');

    // return pending ads
    Route::get('/get-pending', 'getPending');

    // return unaccepte ads
    Route::get('/get-unaccept', 'getUnAccept' );

    // return most searched ads
    Route::get('/get-most-searched', 'getMostSearched');

    // return most viewed ads
    Route::get('/get-most-viewed', 'getMostViewed');

    // return most shared ads
    Route::get('/get-most-shared', 'getMostShared');

    // return most favorited ads
    Route::get('/get-most-favorited', 'getMostFavorited');

    // return most call click ads
    Route::get('/get-most-call-click', 'getMostCallClick');

    // return most whatsapp click ads
    Route::get('/get-most-whatsapp-click', 'getMostWhatsappClick');

    // return most messages click ads
    Route::get('/get-most-messages-click', 'getMostMessagesClick');
 */

    // get results for web
    Route::get('/mobile/data', 'resultMobileData');
    // get results for mobile
    Route::get('/data', 'resultData');
});
