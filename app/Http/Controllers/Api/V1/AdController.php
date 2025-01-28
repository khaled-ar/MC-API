<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\Api\V1\AdFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAdRequest;
use App\Http\Requests\Api\V1\UpdateAdRequest;
use App\Models\Api\V1\Ad;
use App\Notifications\Api\V1\DatabaseUserNotification;
use App\Traits\Api\V1\AdActions;
use App\Traits\Api\V1\Images;
use App\Traits\Api\V1\Results;
use Illuminate\Support\Facades\DB;

class AdController extends Controller {
    use AdActions;

    /**
    * Display a listing of the resource.
    */

    public function __construct() {
        // $this->middleware( 'mobile.verified' )->only( [ 'store', 'update' ] );
    }

    public function index( AdFilters $filters ) {

        $this->authorize( 'viewAny', Ad::class );
        return response()->json( [
            'status' => 1,
            'data' => Ad::active()->with( 'user:username,id' )->filter( $filters )->get(),
        ] );
    }

    // get only pending ads

    public function onlyPending() {

        $this->authorize( 'viewAny', Ad::class );
        return response()->json( [
            'status' => 1,
            'data' => Ad::pending()->with( 'user:username,id' )->get(),
        ] );
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store( StoreAdRequest $request ) {

        $user = $request->user();
        // get user subscription
        $subscription = $user->subscription;

        $request_data_merge = array_merge( [], [ 'user_id' => $user->id ] );

        $request_data_merge = array_merge( $request_data_merge, [ 'pinable' => $subscription->package->pinable ] );

        if ( $user->is_admin ) {
            // do not remove this statement
            $this->authorize( 'create', Ad::class );

            $request_data_merge = array_merge( $request_data_merge, [ 'status' => 'active' ] );
            $request_data_merge = array_merge( $request_data_merge, [ 'approved_by' => $user->id ] );
        }

        if ( $request->type == 'sale' ) {

            // check the limit of sale ads
            if ( $subscription->sale_ads_limit <= 0 && !$user->is_admin ) {
                return response()->json( [
                    'status' => 0,
                    'message' => 'لقد بلغ حد إعلانات البيع لديك الحد الأدنى، قم بشراء باقة ثم حاول لاحقاً'
                ] );

            } else {
                return DB::transaction(
                    fn () => $this->storeSaleAd( $request, $request_data_merge, $subscription )
                );
            }
        } elseif ( $request->type == 'buy' ) {

            // check the limit of buy ads
            if ( $subscription->buy_ads_limit <= 0 && !$user->is_admin ) {
                return response()->json( [
                    'status' => 0,
                    'message' => 'لقد بلغ حد إعلانات الشراء لديك الحد الأدنى، قم بشراء باقة ثم حاول لاحقاً'
                ] );
            } else {
                return DB::transaction(
                    fn () => $this->storeBuyAd( $request, $request_data_merge, $subscription )
                );
            }
        }
    }

    /**
    * Display the specified resource.
    */

    public function show( int $id ) {

        $this->authorize( 'view', Ad::class );

        $ad = Ad::with( [ 'user', 'tags:id,name' ] )
        ->where( 'id', $id )
        ->first();

        return response()->json( [
            'status' => 1,
            'data' => $ad,
        ] );
    }

    /**
    * Update the specified resource in storage.
    */

    public function update( UpdateAdRequest $request, int $id ) {

        $user = request()->user();
        $ad = Ad::firstWhere( 'id', $id );

        if ( !$ad ) {
            return response()->json( [
                'status' => 0,
                'message' => 'الإعلان غير موجود'
            ] );
        }

        if ( $user->is_admin || ( $user->id != $ad->user_id ) ) {
            // do not remove this statement
            $this->authorize( 'update', Ad::class );
        }

        // get user package
        $package = $user->subscription->package;

        $request_data_merge = array_merge( [], [
            'user_id' => ( $user->is_admin && $user->id == $ad->user_id ) ? $user->id : $ad->user_id,
            'status' => ( $user->is_admin && $user->id == $ad->user_id ) ? 'active' : 'pending',
            'pinable' => $package->pinable
        ] );

        if ( $ad->type == 'sale' ) {

            // check sale ad if updateable
            if ( !$user->is_admin && !$package->sale_ads_updateable ) {
                return response()->json( [
                    'status' => 0,
                    'message' => 'الباقة المشترك فيها لا تسمح بتعديل إعلانات البيع'
                ] );

            } else {
                return DB::transaction(
                    fn () => $this->updateSaleAd( $request, $request_data_merge, $ad, $package )
                );
            }
        } elseif ( $ad->type == 'buy' ) {

            // check buy ad if updateable
            if ( !$user->is_admin &&  !$package->buy_ads_updateable ) {
                return response()->json( [
                    'status' => 0,
                    'message' => 'الباقة المشترك فيها لا تسمح بتعديل إعلانات الشراء'
                ] );

            } else {
                return DB::transaction(
                    fn () => $this->updateBuyAd( $request, $request_data_merge, $ad, $package )
                );
            }
        }
    }

    /**
    * Destroy the specified resource from storage.
    */

    public function destroy( int $id ) {

        if ( request()->user()->is_admin ) {
            $this->authorize( 'delete', Ad::class );
        }

        $ad = Ad::where( 'id', $id )->first();

        $ad_images = explode( '|', $ad->images );

        $ad = $ad->forceDelete();

        if ( $ad ) {
            Images::deleteImages( $ad_images, public_path( '/ads_images' ) );
        }

        return response()->json( [
            'status' => $ad ? 1 : 0,
        ] );
    }

    // accept user ad by admin

    public function accept( int $id ) {

        $this->authorize( 'accept', Ad::class );

        $ad = Ad::pending()->where( 'id', $id )->first();

        if ( !$ad ) {
            return response()->json( [
                'status' => 0,
                'message' => 'الإعلان غير موجود'
            ] );
        }
        $user = $ad->user;

        try {
            DB::beginTransaction();

            $ad->update( [
                'status' => 'active',
                'approved_by' => request()->user()->id,
                'created_at' => now()
            ] );

            Results::create( $ad->id, $user->id );

            $user->subscription->decrement( $ad->type == 'sale' ? 'sale_ads_limit' : 'buy_ads_limit' );
            $message = ' لقد تمت الموافقة على إعلانك' . " ({$ad->title}) " . 'من قبل المسؤول';
            $user->notify( new DatabaseUserNotification( $message, 'نظام الإعلانات', 'الموافقة على إعلان' ) );

            $message = 'لقد قام ' . $user->username . ' بنشر إعلان الان';
            $followers = $user->followers()->with( 'user' )->get();
            foreach ( $followers as $follower ) {
                $follower->user->notify( new DatabaseUserNotification( $message, 'نظام الإعلانات', 'إعلان جديد' ) );
            }

            DB::commit();

            return response()->json( [
                'status' => 1
            ] );
        } catch ( \Throwable ) {
            DB::rollBack();
            return response()->json( [
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما',
            ] );
        }
    }

    // unaccept user ad by admin

    public function unaccept( int $id ) {

        $this->authorize( 'unaccept', Ad::class );

        $ad = Ad::where( 'id', $id )->first();

        if ( !$ad ) {
            return response()->json( [
                'status' => 0,
                'message' => 'الإعلان غير موجود'
            ] );
        }

        try {
            DB::beginTransaction();

            $ad->update( [
                'status' => 'unaccept',
                'approved_by' => request()->user()->id
            ] );

            $message = ' لقد تمت رفض إعلانك' . " ({$ad->title}) " . 'من قبل المسؤول';
            $ad->user->notify( new DatabaseUserNotification( $message, 'نظام الإعلانات', 'رفض إعلان' ) );

            DB::commit();

            return response()->json( [
                'status' => 1
            ] );
        } catch ( \Throwable ) {
            DB::rollBack();
            return response()->json( [
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ] );
        }
    }

    // re post user ad

    public function rePost( int $id ) {

        $ad = Ad::where( 'id', $id )->where( 'status', 'inactive' )->first();

        if ( !$ad ) {
            return response()->json( [
                'status' => 0,
                'message' => 'الإعلان غير موجود'
            ] );
        }

        $user = $ad->user;
        if ( $ad->type == 'sale' ) {
            if ( $user->subscription->sale_ads_limit <= 0 ) {
                return response()->json( [
                    'status' => 0,
                    'message' => 'لقد بلغ حد إعلانات الشراء لديك الحد الأدنى، قم بشراء باقة ثم حاول لاحقاً'
                ] );
            }
        } else {
            if ( $user->subscription->buy_ads_limit <= 0 ) {
                return response()->json( [
                    'status' => 0,
                    'message' => 'لقد بلغ حد إعلانات البيع لديك الحد الأدنى، قم بشراء باقة ثم حاول لاحقاً'
                ] );
            }
        }

        try {
            DB::beginTransaction();

            $ad->update( [
                'status' => 'active',
            ] );

            $user->subscription->decrement( $ad->type == 'sale' ? 'sale_ads_limit' : 'buy_ads_limit' );

            DB::commit();

            return response()->json( [
                'status' => 1
            ] );
        } catch ( \Throwable ) {
            DB::rollBack();
            return response()->json( [
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ] );
        }
    }

}
