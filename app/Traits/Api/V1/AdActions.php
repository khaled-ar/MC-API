<?php

namespace App\Traits\Api\V1;

use App\Models\Api\V1\Ad;
use Illuminate\Support\Arr;

trait AdActions {
    use TagActions;

    public function storeSaleAd( $request, $request_data_merge, $subscription ) {

        $images_names = $_FILES[ 'images' ][ 'name' ];
        $images_tmp = $_FILES[ 'images' ][ 'tmp_name' ];

        // Give images random names
        $imagesNames = Images::giveImagesRandomNames( $images_names );

        // store ad in database
        $request_data_merge = array_merge( $request_data_merge, [
            'images' => implode( '|', $imagesNames ),
            'updateable' => $subscription->package->sale_ads_updateable,
            'resultable' => $subscription->package->sale_ads_resultable,
        ] );

        $ad = Ad::create( array_merge( $request->only( [ 'type', 'category_id', 'title', 'description', 'price', 'images', 'phone_number' ] ), $request_data_merge ) );

        if ( !$ad ) {
            return response()->json( [
                'status' => 0,
                'message' => 'يوجد خطأ ما'
            ] );
        }

        Results::create( $ad->id, request()->user()->id );

        // store ad images in public/ads_images
        Images::storeImages( $images_tmp, $imagesNames, public_path( '/ads_images' ) );

        //store new tags to database and attach tags to this ad
        if ( $request->has( 'tags' ) ) {
            $this->storeAdTags( $request, $ad );
        }

        return response()->json( [
            'status' => 1,
        ] );
    }

    public function storeBuyAd( $request, $request_data_merge, $subscription ) {
        $price = $request->min . ' - ' . $request->max;

        // store ad in database
        $request_data_merge = array_merge( $request_data_merge, [
            'price' => $price,
            'updateable' => $subscription->package->buy_ads_updateable,
            'resultable' => $subscription->package->buy_ads_resultable,
        ] );

        $ad = Ad::create( array_merge( $request->only( [ 'type', 'category_id', 'title', 'description', 'price', 'phone_number'  ] ), $request_data_merge ) );

        //store new tags to database and attach tags to this ad
        if ( $request->has( 'tags' ) ) {
            $this->storeAdTags( $request, $ad );
        }

        if ( !$ad ) {
            return response()->json( [
                'status' => 0,
                'message' => 'يوجد خطأ ما'
            ] );
        }

        Results::create( $ad->id, request()->user()->id );

        return response()->json( [
            'status' => 1,
        ] );
    }

    public function updateSaleAd( $request, $request_data_merge, $ad, $package ) {

        $request_data_merge = array_merge( $request_data_merge, [
            'updateable' => $package->sale_ads_updateable,
            'resultable' => $package->sale_ads_resultable,
        ] );

        $images_names = null;
        $images_tmp = null;

        if ( isset( $_FILES[ 'images' ] ) ) {
            $images_names = $_FILES[ 'images' ][ 'name' ];
            $images_tmp = $_FILES[ 'images' ][ 'tmp_name' ];
            $index = array_search( 'default_image.jpg', $images_names );

            if ( $index !== false ) {
                $images_names = array_values( Arr::except( $images_names, $index ) );
                $images_tmp = array_values( Arr::except( $images_tmp, $index ) );
            }
            // Give images random names
            $imagesNames = Images::giveImagesRandomNames( $images_names );
        }

        $ad_images = explode( '|', $ad->images );

        if ( $images_names ) {
            $request_data_merge = array_merge( $request_data_merge, [ 'images' => implode( '|', $imagesNames ) ] );
        }

        // update this ads' tags
        if ($request->has('tags')) {
            $this->storeAdTags($request, $ad);
        }

        // update ad in database
        $ad = $ad->update(array_merge($request->only(['type', 'category_id', 'title', 'description', 'price', 'images', 'phone_number']), $request_data_merge));
        if (!$ad) {
            return response()->json([
                'status' => 0,
                'message' => 'يوجد خطأ ما'
            ]);
        }

        if ($images_names) {
            // delete old ad images in public/ads_images
            Images::deleteImages($ad_images, public_path('/ads_images'));

            // store new ad images in public/ads_images
            Images::storeImages($images_tmp, $imagesNames, public_path('/ads_images'));
        }

        return response()->json([
            'status' => 1,
        ]);
    }

    public function updateBuyAd($request, $request_data_merge, $ad, $package)
    {
        $price = $request->min . ' - ' . $request->max;

        // update ad in database
        $request_data_merge = array_merge($request_data_merge, [
            'price' => $price,
            'updateable' => $package->buy_ads_updateable,
            'resultable' => $package->buy_ads_resultable,
        ]);

        //update this ads' tags
        if ( $request->has( 'tags' ) ) {
            $this->storeAdTags( $request, $ad );
        }

        $ad = $ad->update( array_merge( $request->only( [ 'type', 'category_id', 'title', 'description', 'price', 'phone_number' ] ), $request_data_merge ) );
        if ( !$ad ) {
            return response()->json( [
                'status' => 0,
                'message' => 'يوجد خطأ ما'
            ] );
        }

        return response()->json( [
            'status' => 1,
        ] );
    }
}
