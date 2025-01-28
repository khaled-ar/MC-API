<?php

use App\Classes\Api\V1\Categories;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\ResultController;
use App\Models\Api\V1\Tag;
use App\Traits\Api\V1\Results;
use Illuminate\Support\Facades\Route;
use App\Traits\Api\V1\Countries;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// This route return app key
Route::get('/get-app-key', function() {
    return response()->json([
        'app_key' => env('APP_KEY')
    ]);
});


// Main Route That contain all application routes
Route::middleware('app.key')->group(function () {

    // Auth & Login & Register
    include __DIR__ . '/developers/Api/V1/AuthLoginRegister.php';

    // Forgot & Reset Password
    include __DIR__ . '/developers/Api/V1/Password.php';

    // This route return categories and tags
    Route::get('/get-categories-tags', function() {
        return response()->json([
            'categories' => Categories::getThreeLevels(),
            'tags' => Tag::all(['name']),
        ]);
    });

    // This route return all countries
    Route::get('/countries/{locale?}', function (string $locale = 'ar') {
        return response()->json([
            'status' => 1,
            'countries' => Countries::getCountries($locale),
        ]);
    });

    Route::controller(ResultController::class)->group(function() {

         // increment share click count
        Route::put('/increment-share-count/{id}', 'incrementShareCount');

        // increment call click count
        Route::put('/increment-call-count/{id}', 'incrementCallClick');

        // increment whatsapp click count
        Route::put('/increment-whatsapp-count/{id}', 'incrementWhatsappClick');

        // increment messages click count
        Route::put('/increment-messages-count/{id}', 'incrementMessagesClick');

    });

    Route::middleware('auth:sanctum')->group(function () {

        // Dashboard Results
        Route::get('dashboard/results', function () {
            if(request()->user()->is_admin) {
                return response()->json([
                    'status'=> 1,
                    'section_1' => Results::section1(),
                    'section_2' => Results::section2(),
                    'section_3' => Results::section3(),
                    'section_4' => Results::section4(),
                ]);
            }

            return response()->json([
                'status' => 0,
                'message' => 'ليس لديك الصلاحية لمشاهدة بيانات هذه الصفحة'
            ]);
        });

        // Refresh Token
        Route::post('refresh-token', function() {

            $user = request()->user();
            unset($user['admin']);

            if($user->tokenCan('auth:remember')) {
                $token = request()->bearerToken();
            } else {

                $user->tokens()->delete();
                $token = $user->createToken(
                    'refresh_token',
                    ['*'],
                    now()->addMonth()
                )->plainTextToken;

            }

            return response()->json([
                'status'=> 1,
                'user' => $user,
                'role' => $user->is_admin ? $user->admin->role->name : 'user',
                'token' => $token,
            ]);

        });

        // Logout
        Route::delete('logout', [LogoutController::class, 'logout']);

        // Favorites
        include __DIR__ . '/developers/Api/V1/Favorites.php';

        // Mobile Verification
        // include __DIR__ . '/developers/Api/V1/Mobile.php';

        // Notifications
        include __DIR__ . '/developers/Api/V1/Notifications.php';

        // Payments
        include __DIR__ . '/developers/Api/V1/Payment.php';

        // Roles and Permissions
        include __DIR__ . '/developers/Api/V1/RolesPermissions.php';

        // Categories
        include __DIR__ . '/developers/Api/V1/Categories.php';

        // Ratings
        include __DIR__ . '/developers/Api/V1/Ratings.php';

        // Reports
        include __DIR__ . '/developers/Api/V1/Reports.php';

        // objections
        include __DIR__ . '/developers/Api/V1/Objections.php';

        // Results
        include __DIR__ . '/developers/Api/V1/Results.php';

        // Ads
        include __DIR__ . '/developers/Api/V1/Ads.php';

        // offers
        include __DIR__ . '/developers/Api/V1/Offers.php';

        // Followings
        include __DIR__ . '/developers/Api/V1/Followings.php';

        // Comments
        include __DIR__ . '/developers/Api/V1/Comments.php';

        // Messages
        include __DIR__ . '/developers/Api/V1/Messages.php';

        // Admins
        include __DIR__ . '/developers/Api/V1/Admins.php';
    });

    // Packages
    include __DIR__ . '/developers/Api/V1/Packages.php';

    // Home Page
    include __DIR__ . '/developers/Api/V1/Home.php';

    // Users
    include __DIR__ . '/developers/Api/V1/Users.php';
});
