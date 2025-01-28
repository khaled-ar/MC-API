<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\FollowUser;
use App\Notifications\Api\V1\DatabaseUserNotification;
use Illuminate\Support\Facades\DB;

class FollowUserController extends Controller {

    // Follows a user

    public function followUser( int $id ) {

        $follower = request()->user();
        $following = FollowUser::where( 'user_id', $id )->where( 'follower_id', $follower->id )->first();

        if ( $following ) {
            return response()->json( [
                'status' => 0,
            ] );
        }

        try {

            DB::beginTransaction();
            $following = FollowUser::create( [
                'user_id' => $id,
                'follower_id' => $follower->id,
            ] );

            $message = 'لقد قام ' . $follower->username . ' بمتابعتك';
            $following->user->notify( new DatabaseUserNotification( $message, 'نظام المستخدمين', 'عملية متابعة جديدة' ) );

            DB::commit();
            return response()->json( [
                'status' => 1,
            ] );

        } catch ( \Throwable ) {
            DB::rollBack();
            return response()->json( [
                'status' => 0,
                'message' => 'يوجد خطأ ما'
            ] );
        }
    }

    // Unfollows a user

    public function unFollowUser( int $id ) {

        $follower_id = request()->user()->id;
        $following = FollowUser::where( 'user_id', $id )->where( 'follower_id', $follower_id )->delete();

        return response()->json( [
            'status' => $following ? 1 : 0,
        ] );
    }

    // Retrieves followers of the authenticated user

    public function getFollowers() {

        return response()->json( [
            'status' => 1,
            'data' => request()->user()->followers,
        ] );
    }
}
