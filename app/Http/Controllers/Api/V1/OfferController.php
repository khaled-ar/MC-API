<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOfferRequest;
use App\Http\Requests\Api\V1\UpdateOfferRequest;
use App\Models\Api\V1\Offer;
use App\Notifications\Api\V1\DatabaseUserNotification;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller {

    public function __construct() {
        // $this->middleware( 'mobile.verified' )->only( [ 'store', 'update' ] );
    }

    // Retrieves all offers

    public function index() {

        $this->authorize( 'viewAny', Offer::class );
        $offers = Offer::with( 'user', 'ad' )->get();

        return response()->json( [
            'status' => 1,
            'data' => $offers,
        ] );
    }

    // Show single offer

    public function show( int $id ) {

        $this->authorize( 'view', Offer::class );
        $offer = Offer::where( 'id', $id )->with( 'user', 'ad' )->first();

        return response()->json( [
            'status' => 1,
            'offer' => $offer,
        ] );
    }

    // Stores a new offer

    public function store( StoreOfferRequest $request ) {
        $user = $request->user();

        if ( $user->subscription->offers_limit <= 0 ) {
            return response()->json( [
                'status' => 0,
                'message' => 'عذراً لا يوجد لديك ما يكفي من العروض في الباقة المشترك فيها'
            ] );
        }

        $offer = Offer::where( 'ad_id', $request->ad_id )
        ->where( 'user_id', $user->id )
        ->where( 'content', $request->content )
        ->where( 'value', $request->value )
        ->first();

        if ( $offer ) {
            return response()->json( [
                'status' => 0,
                'message' => 'العرض موجود مسبقا'
            ] );
        }

        Offer::create( array_merge( $request->safe()->all(), [
            'user_id' => $user->id,
            'offer_highlighting' => $user->subscription->package->offer_highlighting,
            'type' => $user->subscription->package->hide_offer ? 'hidden' : 'visible'
        ] ) );

        return response()->json( [
            'status' => 1,
        ] );
    }

    // Updates an existing offer

    public function update( UpdateOfferRequest $request, int $id ) {

        $user = $request->user();
        $offer = Offer::firstWhere( 'id', $id );

        if ( !$offer ) {
            return response()->json( [
                'status' => 0,
            ] );
        }

        if ( $user->id !== $offer->user_id ) {
            return response()->json( [
                'status' => 0,
                'message' => 'غير مخول'
            ] );
        }
        $offer->update( $request->safe()->all() );

        return response()->json( [
            'status' => 1
        ] );
    }

    // Deletes an offer

    public function destroy( int $id ) {
        $offer = Offer::firstWhere( 'id', $id );

        if ( !$offer ) {
            return response()->json( [
                'status' => 0,
            ] );
        }

        if ( request()->user()->id !== $offer->user_id ) {
            return response()->json( [
                'status' => 0,
                'message' => 'غير مخول'
            ] );
        }

        $deleted = $offer->delete();
        return response()->json( [
            'status' => $deleted ? 1 : 0
        ] );
    }

    // unaccept an offer

    public function unaccept( int $id ) {

        $this->authorize( 'delete', Offer::class );
        $offer = Offer::where( 'id', $id )->first();
        try {
            DB::beginTransaction();
            $ad = $offer->ad;
            $message = 'لقد تم رفض عرضك من قبل المسؤول، ' . $ad->title;
            $offer->user->notify( new DatabaseUserNotification( $message, 'نظام العروض', ' رفض عرض' ) );
            $offer->delete();
            DB::commit();
            return response()->json( [
                'status' => 1,
            ] );

        } catch( \Throwable ) {
            DB::rollBack();
            return response()->json( [
                'status' => 0,
            ] );
        }
    }

    // accept an offer

    public function accept( int $id ) {

        $this->authorize( 'accept', Offer::class );
        $offer = Offer::where( 'id', $id )->first();

        try {
            DB::beginTransaction();
            $user = $offer->user;

            $offer->update( [
                'status' => 'active',
            ] );
            $user->subscription->decrement( 'offers_limit' );

            $ad = $offer->ad;
            $message = 'لقد تمت الموافقة على عرضك من قبل المسؤول، ' . $ad->title;
            $user->notify( new DatabaseUserNotification( $message, 'نظام العروض', 'الموافقة على عرض' ) );

            $message = ' لقد قام ' . $offer->user->username . ' بتقديم عرض على ' . $ad->title;
            $offer->ad->user->notify( new DatabaseUserNotification( $message, 'نظام العروض', 'عرض جديد' ) );

            DB::commit();
            return response()->json( [
                'status' => 1,
            ] );

        } catch( \Throwable ) {

            DB::rollBack();
            return response()->json( [
                'status' => 0,
            ] );
        }
    }
}
