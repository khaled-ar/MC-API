<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\Api\V1\AdFilters;
use App\Http\Controllers\Controller;
use App\Models\Api\V1\Ad;
use App\Models\Api\V1\Category;
use App\Models\Api\V1\Favorite;
use App\Traits\Api\V1\Results;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller {

    //get favorites for specific user

    public function index( AdFilters $filters ) {

        $favorites = request()->user()->favorites()
        ->pluck( 'id', 'ad_id' )->toArray();

        if ( request( 'category' ) ) {
            $category = Category::active()->where( 'name', request( 'category' ) )->first();
            if ( $category ) {
                $ads = Ad::active()->where( 'category_id', $category->id )
                ->with( 'user:id,username' )
                ->whereIn( 'ads.id', array_keys( $favorites ) )
                ->filter( $filters )->get();
            } else {
                $ads = [];
            }

        } else {

            $ads = Ad::active()->with( 'user:id,username' )
            ->whereIn( 'ads.id', array_keys( $favorites ) )
            ->filter( $filters )->get();
        }

        foreach ( $ads as $ad ) {
            $ad->fav_id = $favorites[ $ad->id ];
        }

        $similar_ads = [];
        foreach ( $ads as $ad ) {
            $tags = $ad->tags;
            foreach ( $tags as $tag ) {
                $similar_ads[] = $tag->ads()->active()->where( 'ad_id', '<>', $tag->pivot->ad_id )->with( 'user:id,username' )->get();
            }
        }

        return response()->json( [
            'status' => 1,
            'data' => $ads,
            'similar' => $similar_ads[ 0 ] ?? []
        ] );
    }

    //add ad to favorites for specific user

    public function store( int $ad_id ) {

        $user = request()->user();
        $fav = Favorite::where( 'user_id', $user->id )->where( 'ad_id', $ad_id )->first();

        if ( ! $fav ) {
            try {
                DB::beginTransaction();

                $fav = Favorite::forceCreate( [
                    'user_id' => $user->id,
                    'ad_id' => $ad_id,
                ] );

                Results::increment( $ad_id, 'favorited_count' );

                DB::commit();

                return response()->json( [
                    'status' => 1,
                    'message' => 'تمت إضافة الإعلان إلى المفضلة',
                    'fav_id' => $fav->id,
                ] );

            } catch( \Throwable $e ) {
                DB::rollBack();
                return response()->json( [
                    'status' => 0,
                    'message' => 'يوجد خطأ ما'
                ] );
            }
        }

        return response()->json( [
            'status' => 0,
            'message' => 'الإعلان موجود مسبقا'
        ] );
    }

    //delete ad from favorites for specific user

    public function delete( int $fav_id ) {

        try {
            DB::beginTransaction();
            $fav = Favorite::where( 'id', $fav_id )->first();

            if ( Favorite::destroy( $fav_id ) ) {
                Results::decrement( $fav->ad_id, 'favorited_count' );
                DB::commit();
                return response()->json( [
                    'status' => 1,
                    'message' => 'تمت إزالة الإعلان من المفضلة'
                ] );
            }

            return response()->json( [
                'status' => 0,
                'message' => 'الإعلان غير موجود'
            ] );

        } catch( \Throwable $e ) {
            DB::rollBack();
            die ( $e->getMessage() );
        }
    }

}
