<?php

namespace App\Traits\Api\V1;

use App\Models\Api\V1\Ad;
use App\Models\Api\V1\Category;
use App\Models\Api\V1\Offer;
use App\Models\Api\V1\Payment;
use App\Models\Api\V1\Result;
use App\Models\Api\V1\User;
use App\Models\Api\V1\Visitor;
use App\Notifications\Api\V1\DatabaseUserNotification;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\DB;

trait Results {

    protected static $year;
    protected static $month;

    // this function to increment A specific column for a specific ad
    public static function increment( int $ad_id, string $column ) {

        if ( $column == 'search_count' ) {
            $ip = request()->ip();
            // get ad results
            $result = Result::where( 'ad_id', $ad_id )->first();
            // get actors data
            $actors = Json::decode( $result->actors );
            // this foreach searching for auth user and action in actors data
            foreach ( $actors ?? [] as $actor ) {
                if ( isset( $actor[ $ip ] ) && $actor[ $ip ] == $column ) {
                    return;
                }
            }

            if ( $result->increment( $column ) ) {
                $actors[] = [ $ip => $column ];
                $result->actors = Json::encode( $actors );
                $result->save();
                return;
            }
        }

        $user = auth( 'sanctum' )->user();
        if ( $user ) {
            // get ad results
            $result = Result::where( 'ad_id', $ad_id )->first();
            // get actors data
            $actors = Json::decode( $result->actors );

            if ( $result ) {
                try {
                    DB::beginTransaction();
                    // this foreach searching for auth user and action in actors data
                    foreach ( $actors ?? [] as $actor ) {
                        if ( isset( $actor[ $user->id ] ) && $actor[ $user->id ] == $column ) {
                            return response()->json( [
                                'status' => 1
                            ] );
                        }
                    }
                    // increment the required column value
                    if ( $result->increment( $column ) ) {
                        $actors[] = [ $user->id => $column ];
                        $result->actors = Json::encode( $actors );
                        $result->save();

                        if ( in_array( $column, [ 'call_click_count', 'whatsapp_click_count', 'messages_click_count' ] ) ) {
                            // notify the user
                            $message = 'يريد ' . $user->username . ' التواصل معك على ';
                            if ( $column == 'call_click_count' ) {
                                $message .= 'المكالمات';
                            } elseif ( $column == 'whatsapp_click_count' ) {
                                $message .= 'واتساب';
                            } else {
                                $message .= 'الرسائل';
                            }
                            $result->user->notify( new DatabaseUserNotification( $message, 'نظام الإعلانات', 'إشعار بالتواصل' ) );
                        }
                    }
                    DB::commit();
                    return response()->json( [
                        'status' => 1
                    ] );
                } catch( \Throwable ) {
                    DB::rollBack();
                    return response()->json( [
                        'status' => 0
                    ] );
                }
            } else {
                return response()->json( [
                    'status' => 0
                ] );
            }

        }
    }

    // this function to decrement A specific column for a specific ad

    public static function decrement( int $ad_id, string $column ) {

        $user = auth( 'sanctum' )->user();
        if ( $user ) {
            // get ad results
            $result = Result::where( 'ad_id', $ad_id )->first();
            // get actors data
            $actors = Json::decode( $result->actors );
            if ( $result ) {
                $data_key = null;
                // this foreach searching for auth user and action in actors data
                foreach ( $actors ?? [] as $key => $actor ) {
                    if ( isset( $actor[ $user->id ] ) && $actor[ $user->id ] == $column ) {
                        $data_key = $key;
                    }
                }

                if ( $data_key ) {
                    // decrement the required column value
                    if ( $result->decrement( $column ) ) {
                        unset( $actors[ $data_key ] );
                        $result->actors = Json::encode( $actors );
                        $result->save();
                    }
                }
                return response()->json( [
                    'status' => 0
                ] );

            } else {
                return response()->json( [
                    'status' => 0
                ] );
            }

        } else {
            return response()->json( [
                'status' => 0
            ] );
        }
    }

    // this function to create a new record in results table for a specific ad
    public static function create( int $ad_id, int $user_id ) {

        $result = Result::where( 'ad_id', $ad_id )->where( 'user_id', $user_id )->first();

        if ( ! $result ) {
            Result::create( [
                'ad_id' => $ad_id,
                'user_id' => $user_id,
            ] );
        }
    }

    // admin section

    // add visitor
    public static function visitor() {

        $ip = request()->ip();
        $visitor = Visitor::where( 'ip_address', $ip )->first();
        if ( !$visitor ) {
            Visitor::create( [ 'ip_address' => $ip ] );
        }

    }

    // section 1 functions

    // get section 1 data
    public static function section1() {

        static::$year = request( 'year' ) ?? now()->format( 'Y' );
        static::$month = request( 'month' ) ?? now()->format( 'm' );

        return [
            'visitors_count'     => static::getVisitorsCount(),
            'visitors_ratio'     => static::getVisitorsRatio(),
            'active_users_count' => static::getActiveUsersCount(),
            'active_users_ratio' => static::getActiveUsersRatio(),
            'new_users_count'    => static::getNewUsersCount(),
            'new_users_ratio'    => static::getNewUsersRatio(),
            'ads_count'          => static::getAdsCount(),
            'ads_ratio'          => static::getAdsRatio(),
            'offers_count'       => static::getOffersCount(),
            'offers_ratio'       => static::getOffersRatio(),
        ];
    }

    // return visitors count with visitors ratio
    public static function getVisitorsCount() {
        return Visitor::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();
    }

    // get visitors ratio
    public static function getVisitorsRatio() {
        $pre_month_count = Visitor::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month - 1 )->count();

        $cur_month_count = Visitor::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )->count();

        $final_ratio = ( $cur_month_count - $pre_month_count ) * 100 / ( $pre_month_count ? $pre_month_count : 100 );
        $final_ratio = number_format( $final_ratio, 2 );
        return  ( $final_ratio > 0 ? '+' . $final_ratio : $final_ratio );
    }

    // get active users count
    public static function getActiveUsersCount() {
        return User::where( 'status', 'active' )
        ->whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();
    }

    // get active users ration
    public static function getActiveUsersRatio() {
        $pre_month_count = User::where( 'status', 'active' )
        ->whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month - 1 )
        ->count();

        $cur_month_count = User::where( 'status', 'active' )
        ->whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();

        $final_ratio = ( $cur_month_count - $pre_month_count ) * 100 / ( $pre_month_count ? $pre_month_count : 100 );
        $final_ratio = number_format( $final_ratio, 2 );
        return  ( $final_ratio > 0 ? '+' . $final_ratio : $final_ratio );
    }

    // get new users
    public static function getNewUsersCount() {
        return User::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();
    }

    // get new users ration
    public static function getNewUsersRatio() {
        $pre_month_count = User::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month - 1 )
        ->count();

        $cur_month_count = User::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();

        $final_ratio = ( $cur_month_count - $pre_month_count ) * 100 / ( $pre_month_count ? $pre_month_count : 100 );
        $final_ratio = number_format( $final_ratio, 2 );
        return  ( $final_ratio > 0 ? '+' . $final_ratio : $final_ratio );
    }

    // get ads count
    public static function getAdsCount() {
        return Ad::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();
    }

    // get ads ration
    public static function getAdsRatio() {
        $pre_month_count = Ad::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month - 1 )
        ->count();

        $cur_month_count = Ad::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();

        $final_ratio = ( $cur_month_count - $pre_month_count ) * 100 / ( $pre_month_count ? $pre_month_count : 100 );
        $final_ratio = number_format( $final_ratio, 2 );
        return  ( $final_ratio > 0 ? '+' . $final_ratio : $final_ratio );
    }

    // get offers count
    public static function getOffersCount() {
        return Offer::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();
    }

    // get offers ration
    public static function getOffersRatio() {
        $pre_month_count = Offer::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month - 1 )
        ->count();

        $cur_month_count = Offer::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();

        $final_ratio = ( $cur_month_count - $pre_month_count ) * 100 / ( $pre_month_count ? $pre_month_count : 100 );
        $final_ratio = number_format( $final_ratio, 2 );
        return  ( $final_ratio > 0 ? '+' . $final_ratio : $final_ratio );
    }

    // section 2 functions

    // get section 2 data
    public static function section2() {
        return [
            'users_count'        => static::getUsers12MonthCount(),
            'sale_ads_count'     => static::getSaleAds12MonthCount(),
            'buy_ads_count'      => static::getBuyAds12MonthCount(),
            'offers_count'       => static::getOffers12MonthCount(),
        ];
    }

    // get users count in every month

    public static function getUsers12MonthCount() {
        $data = [];
        for ( $i = 1; $i <= 12; $i++ ) {
            $data[] = User::whereYear( 'created_at', static::$year )
            ->whereMonth( 'created_at', $i )
            ->count();
        }
        return $data;
    }

    // get sale ads count in every month

    public static function getSaleAds12MonthCount() {
        $data = [];
        for ( $i = 1; $i <= 12; $i++ ) {
            $data[] = Ad::whereYear( 'created_at', static::$year )
            ->whereMonth( 'created_at', $i )
            ->where( 'type', 'sale' )
            ->count();
        }
        return $data;
    }

    // get buy ads count in every month

    public static function getBuyAds12MonthCount() {
        $data = [];
        for ( $i = 1; $i <= 12; $i++ ) {
            $data[] = Ad::whereYear( 'created_at', static::$year )
            ->whereMonth( 'created_at', $i )
            ->where( 'type', 'buy' )
            ->count();
        }
        return $data;
    }

    // get offe
    public static function getOffers12MonthCount() {
        $data = [];
        for ( $i = 1; $i <= 12; $i++ ) {
            $data[] = Offer::whereYear( 'created_at', static::$year )
            ->whereMonth( 'created_at', $i )->count();
        }
        return $data;
    }

    // section 3 functions

    // get section 3 data
    public static function section3() {
        return [
            'categories' => request()->has( 'most_searched' )
            ?
            static::mostSearchedCategories()
            :
            static::popularCategories(),
        ];
    }

    // get popular categories
    public static function popularCategories() {
        $popular_categories = Category::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->select( 'name' )
        ->withCount( 'ads' )
        ->orderByDesc( 'ads_count' )
        ->take( 12 )
        ->get();

        $final_categories = [];
        $ads_count = static::getAdsCount();

        foreach ( $popular_categories as $category ) {
            $final_categories[] = [
                'name' => $category->name,
                'ratio' => number_format( $category->ads_count * 100 / ( $ads_count ? $ads_count : 100 ) )
            ];
        }

        return $final_categories;
    }

    // get most searched categories
    public static function mostSearchedCategories() {
        $most_searched_categories = Category::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->select( 'name', 'search_count' )
        ->orderByDesc( 'search_count' )
        ->take( 12 )
        ->get();

        $final_categories = [];
        $total_search = Category::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->sum( 'search_count' );

        foreach ( $most_searched_categories as $category ) {
            $final_categories[] = [
                'name' => $category->name,
                'ratio' => number_format( $category->search_count * 100 / ( $total_search ? $total_search : 100 ) )
            ];
        }

        return $final_categories;
    }

    // section 4 functions

    // get section 4 data
    public static function section4() {
        return [
            'messages_count'        => static::getClicksCount( 'messages_click_count' ),
            'messages_ratio'        => static::getClicksRatio( 'messages_click_count' ),
            'call_count'            => static::getClicksCount( 'call_click_count' ),
            'call_ratio'            => static::getClicksRatio( 'call_click_count' ),
            'whatsapp_count'        => static::getClicksCount( 'whatsapp_click_count' ),
            'whatsapp_ratio'        => static::getClicksRatio( 'whatsapp_click_count' ),
            'subscriptions_count'   => static::getSubscriptionsCount(),
            'subscriptions_ratio'   => static::getSubscriptionsRatio(),
        ];
    }

    // get clicks count
    public static function getClicksCount( string $column ) {
        return Result::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->sum( $column );
    }

    // get clicks count
    public static function getClicksRatio( string $column ) {
        $pre_month_count = Result::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month - 1 )
        ->sum( $column );

        $cur_month_count = Result::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->sum( $column );

        $final_ratio = ( $cur_month_count - $pre_month_count ) * 100 / ( $pre_month_count ? $pre_month_count : 10000 );
        $final_ratio = number_format( $final_ratio, 2 );
        return  ( $final_ratio > 0 ? '+' . $final_ratio : $final_ratio );
    }

    // get supscriptions count
    public static function getSubscriptionsCount() {
        return Payment::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();
    }

    // get supscription ratio
    public static function getSubscriptionsRatio() {
        $pre_month_count = Payment::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month - 1 )
        ->count();

        $cur_month_count = Payment::whereYear( 'created_at', static::$year )
        ->whereMonth( 'created_at', static::$month )
        ->count();

        $final_ratio = ( $cur_month_count - $pre_month_count ) * 100 / ( $pre_month_count ? $pre_month_count : 100 );
        $final_ratio = number_format( $final_ratio, 2 );
        return  ( $final_ratio > 0 ? '+' . $final_ratio : $final_ratio );
    }
}
