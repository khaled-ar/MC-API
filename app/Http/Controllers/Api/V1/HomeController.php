<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\Api\V1\AdFilters;
use App\Classes\Api\V1\Categories;
use App\Http\Controllers\Controller;
use App\Models\Api\V1\Ad;
use App\Models\Api\V1\Category;
use App\Traits\Api\V1\Results;

class HomeController extends Controller {

    // show single ad from home page

    public function showAd( int $ad_id ) {

        $ad = Ad::active()->with( [
            'user'           => fn( $query ) => $query->select( [ 'id', 'username', 'phone_number', 'whatsapp', 'image' ] ),
            'tags',
            'comments.user'  => fn( $query ) => $query->select( [ 'id', 'username', 'image' ] ),
            'offers.user'    => fn( $query ) => $query->select( [ 'id', 'username', 'image' ] )
        ] )->where( 'id', $ad_id )->first();

        if ( $ad ) {
            Results::increment( $ad_id, 'view_count' );
        }

        $tags = $ad->tags ?? [];
        $similar_ads = [];
        foreach ( $tags as $tag ) {
            $similar_ads[] = $tag->ads()->active()->where( 'ad_id', '<>', $tag->pivot->ad_id )->with( 'user:id,username' )->get();
        }

        if ( auth( 'sanctum' )->check() ) {
            $user_ad = auth( 'sanctum' )->user()->id;
            if ( $ad->user_id === $user_ad ) {
                foreach ( $ad->offers as $offer ) {
                    $offer->type = 'visible';
                }
            }
        }

        return response()->json( [
            'status' => 1,
            'data' => $ad,
            'similar_ads' => $similar_ads[ 0 ] ?? [],
        ] );
    }

    // show single category from home page

    public function showCategory( AdFilters $filters, int $category_id ) {
        $category = Category::with( [
            'ads' => fn ( $query ) => $query->filter( $filters )->orderByDesc( 'pinable' ),
            'ads.user:id,username',
        ] )
        ->withCount( 'ads' )
        ->where( 'id', $category_id )->first();

        return response()->json( [
            'status' => 1,
            'data' => $category,
        ] );
    }

    // this function return home data of mobile app

    public function homeMobileData( AdFilters $filters ) {

        Results::visitor();

        $categories = Category::active()->get( [ 'id', 'name', 'image' ] );

        if ( request( 'category' ) ) {
            $category = Category::where( 'name', request( 'category' ) )->first();
            if ( $category ) {

                $ads = Ad::active()->where( 'category_id', $category->id )->with( [
                    'user' => fn ( $query ) => $query->select( [ 'id', 'username' ] ),
                ] )->filter( $filters )->orderByDesc( 'pinable' )->get( [
                    'id', 'user_id', 'title', 'description',
                    'created_at', 'pinable', 'price', 'type', 'images'
                ] );

            } else {
                $ads = [];
            }

        } else {

            $ads = Ad::active()->with( [
                'user' => fn ( $query ) => $query->select( [ 'id', 'username' ] ),
            ] )->filter( $filters )->orderByDesc( 'pinable' )->get( [
                'id', 'user_id', 'title', 'description',
                'created_at', 'pinable', 'price', 'type', 'images'
            ] );
        }

        return response()->json( [
            'status'=> 1,
            'categories' => $categories,
            'ads' => $ads,
        ] );
    }

    // categories menu

    public function menu() {
        return Categories::getThreeLevels();
    }

    // Popular Categories

    public function popular() {
        $popularCategories = Category::select( 'id', 'name' )->active()
        ->has( 'ads' )->withCount( 'ads' )->orderByDesc( 'ads_count' )->take( 50 )->get();

        return $popularCategories;
    }

    // get all categories names with ads and tags

    public function allWithAdsTags( AdFilters $filters ) {

        if ( request( 'category' ) ) {
            $categories = Category::with( [
                'ads' => fn ( $query ) => $query->select( [
                    'id', 'user_id', 'title', 'description',
                    'created_at', 'pinable', 'price', 'type', 'category_id'
                ] )->filter( $filters )->orderByDesc( 'pinable' )
            ] )->active()->where( 'name', request( 'category' ) )->get( [ 'id', 'name', 'image' ] );

            if ( $categories->first() ) {
                $categories->first()->increment( 'search_count' );
            }

        } else {

            // get active categories with ads
            $categories = Category::with( [
                'ads' => fn ( $query ) => $query->select( [
                    'id', 'user_id', 'title', 'description',
                    'created_at', 'pinable', 'price', 'type', 'category_id', 'images'
                ] )->filter( $filters )->orderByDesc( 'pinable' )
            ] )->active()->has( 'ads' )->get( [ 'id', 'name', 'image' ] );
        }

        // here we are get all tags from all ads in same category
        foreach ( $categories as $category ) {
            $ads = $category->ads;
            $category_tags = [];
            foreach ( $ads as $ad ) {
                $ad_tags = $ad->tags;
                $ad->load( [
                    'user:id,username'
                ] );
                unset( $ad->tags );
                foreach ( $ad_tags as $tag ) {
                    $category_tags[] = collect( [ 'id' => $tag->id, 'name' => $tag->name ] ) ;
                }
            }
            $category->tags = array_values( array_unique( $category_tags ) );
        }

        return $categories;
    }

    // Store Home data in cache storage

    public function homeData( AdFilters $filters ) {

        Results::visitor();

        return response()->json( [
            'menu'    => $this->menu(),
            'popular' => $this->popular(),
            'all_ads' => Ad::with( 'user:id,username' )->where( 'status', 'active' )->orderByDesc( 'pinable' )->latest('id')->get(),
            'all'     => $this->allWithAdsTags( $filters ),
        ] );
    }
}
