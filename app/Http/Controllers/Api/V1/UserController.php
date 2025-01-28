<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\Api\V1\UserFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Models\Api\V1\Result;
use App\Models\Api\V1\User;
use App\Traits\Api\V1\Images;
use App\Traits\Api\V1\UserAction;
use Illuminate\Support\Facades\DB;
use App\Traits\Api\V1\UpdateUserSubscription;

class UserController extends Controller
{
    use UserAction;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['register']);
    }

    // get all users data [admin]

    public function index(UserFilters $filters)
    {
        $this->authorize('viewAny', User::class);

        $users = User::filter($filters)->get();
        return response()->json([
            'status' => 1,
            'data' => $users
        ]);
    }

    // get user profile data [anyone]

    public function profile(int $id)
    {
        $user = User::where('id', $id)->withCount(['comments', 'offers', 'ads'])->first();
        $follwoers = $user->followers()->pluck('follower_id')->toArray();
        $user->followers_count = count($follwoers);

        if(in_array(request()->user()->id, $follwoers)) {
            $user->follow_btn = false;
        } else {
            $user->follow_btn = true;
        }

        return response()->json([
            'status' => 1,
            'data' => $user
        ]);
    }

    // get user account results [user]

    public function results()
    {
        $user = request()->user()->loadCount(['followers', 'comments', 'offers', 'ads']);

        $user->total_whatsapp = (int) Result::where('user_id', $user->id)->sum('whatsapp_click_count');
        $user->total_messages = (int) Result::where('user_id', $user->id)->sum('messages_click_count');
        $user->total_call     = (int) Result::where('user_id', $user->id)->sum('call_click_count');

        return response()->json([
            'status' => 1,
            'user' => $user
        ]);
    }

    // get user account subscriptions [user]

    public function subscriptions()
    {
        $user = request()->user();
        $subscription_package = $user->subscription->package;
        $payments = $user->payments()->latest('id')->get();

        $subscriptions[] = [
            'id' => $subscription_package->id,
            'name' => $subscription_package->name,
            'type' => $subscription_package->type,
            'created_at' => $subscription_package->created_at->format('Y-m-d'),
            'expiry_at' => $subscription_package->created_at->addDays($subscription_package->validity)->format('Y-m-d'),
            'subscription_status' => 1
        ];

        foreach($payments ?? [] as $payment) {
            $package = $payment->package;
            if($package->id != $subscription_package->id) {
                $package->expiry_at = $payment->created_at->addDays($package->validity);
                $subscriptions[] = [
                'id' => $package->id,
                'name' => $package->name,
                'type' => $package->type,
                'created_at' => $payment->created_at->format('Y-m-d'),
                'expiry_at' => $package->expiry_at->format('Y-m-d'),
                'subscription_status' => 0
                ];
            }
        }

        return response()->json([
            'status' => 1,
            'subscriptions_count' => count($subscriptions),
            'subscriptions' => $subscriptions
        ]);
    }

    // get user data [admin]

    public function show(int $id)
    {
        $this->authorize('view', User::class);

        $user = User::withTrashed()->firstWhere('id', $id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        return response()->json([
            'status' => 1,
            'data' => $user
        ]);
    }

    // store user account data [admin]

    public function store(StoreUserRequest $request) {
        return $this->createAccount($request);
    }

    // create a new account [viewer]

    public function register(StoreUserRequest $request) {
        return $this->createAccount($request);
    }

    // update user data [anyone]

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::firstWhere('id', $id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        if (request()->user()->is_admin || request()->user()->id != $user->id) {
            $this->authorize('update', User::class);
        }

        return DB::transaction(function () use ($user, $request) {

            $user = $this->updateUser($user, $request);

            return response()->json([
                'status' => 1,
                'user' => $user,
            ]);
        });
    }

    // delete user account [anyone]

    public function delete($id)
    {
        $user = User::firstWhere('id', $id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        if (request()->user()->id != $user->id) {
            $this->authorize('delete', User::class);
        }

        return DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $deleted = $user->delete();

            return response()->json([
                'status' => $deleted ? 1 : 0,
            ]);

        });

    }

    // get users deleting requests [admin]

    public function pendingDelete()
    {
        $this->authorize('viewAny', User::class);

        $users = User::onlyTrashed()->get();
        return response()->json([
            'status' => 1,
            'data' => $users
        ]);
    }

    // get blocked users [admin]

    public function blocked()
    {
        $this->authorize('viewAny', User::class);

        $users = User::where('status', 'blocked')->get();
        return response()->json([
            'status' => 1,
            'data' => $users
        ]);
    }

    // restore user account (reject user deleting request) [admin]

    public function restore($id)
    {
        $this->authorize('restore', User::class);

        $user = User::onlyTrashed()->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        $user = $user->restore();

        return response()->json([
            'status' => 1,
        ]);
    }

    // destroy user account (accept user deleting request) [admin]

    public function destroy($id)
    {
        $this->authorize('forceDelete', User::class);

        $user = User::withTrashed()->firstWhere('id', $id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        $user_image = $user->image;
        $deleted = $user->forceDelete();
        if ($deleted) {

            Images::deleteImages([$user_image], public_path('/profiles_pictures'));

            return response()->json([
                'status' => 1,
            ]);
        }
    }

    // block the user [admin]

    public function block($id)
    {
        $this->authorize('block', User::class);

        $user = User::firstWhere('id', $id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        $user->status = 'blocked';
        $user->save();

        return response()->json([
            'status' => 1,
        ]);
    }

    // unblock the user [admin]

    public function unBlock($id)
    {
        $this->authorize('block', User::class);

        $user = User::firstWhere('id', $id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'المستخدم غير موجود'
            ]);
        }

        $user->status = 'active';
        $user->save();

        return response()->json([
            'status' => 1,
        ]);
    }


    // create account
    protected function createAccount(StoreUserRequest $request) {

        return DB::transaction(function () use ($request) {

            $user = $this->storeUser($request);

            UpdateUserSubscription::defaultSubsicribe($user);

            $token = $user->createToken(
                'register_token',
                ['*'],
                now()->addMonth()
            )->plainTextToken;

            return response()->json([
                'status' => 1,
                // 'message' => 'تمت عملية التسجيل بنجاح الرجاء تأكيد رقم الموبايل',
                'user' => $user,
                'token' => $token
            ]);
        });
    }
}
