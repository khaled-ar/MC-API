<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\Api\V1\AdFilters;
use App\Http\Controllers\Controller;
use App\Models\Api\V1\Ad;
use App\Traits\Api\V1\Results;

class ResultController extends Controller {

    // get active ads

    public function getActive( AdFilters $filters ) {
        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()->active()->filter( $filters )->get();
    }

    // get pending ads

    public function getPending( AdFilters $filters ) {
        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()->filter( $filters )->pending()->get();
    }

    // get updateable ads

    public function getUpdateable() {

        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()->where( 'updateable', 1 )->get();
    }

    // get inactive ads

    public function getInactive() {

        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()->where( 'status', 'inactive' )->get();
    }

    // get active offers ads

    public function getActiveOffers() {
        $user = request()->user();

        return $user->offers()->with( 'ad' )->where( 'status', 'active' )->get();
    }

    // get almost inactive ads

    public function getAlmostInactive() {
        $user = request()->user();
        $subscription = $user->subscription;
        $ads = [];
        $user_ads = $user->ads()->with( [ 'category:id,name', 'tags' ] )
        ->where( 'status', 'active' )->get();
        foreach ( $user_ads as $ad ) {
            $validity = $ad->type == 'sale' ? $subscription->sale_ads_validity : $subscription->buy_ads_validity;
            if ( ( now()->format( 'd' ) == $ad->created_at->addDays( $validity - 7 )->format( 'd' ) ) || $validity == 7 ) {
                $ads[] = $ad;
            }
        }

        return $ads;
    }

    // get unaccept ads

    public function getUnAccept() {
        return Ad::with( [ 'category:id,name', 'tags' ] )->owner()->unaccept()->get();
    }

    // get most searched ads

    public function getMostSearched() {
        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()
        ->resultable()
        ->join( 'results', 'results.ad_id', '=', 'ads.id' )
        ->orderByDesc( 'results.search_count' )
        ->take( 20 )
        ->get();
    }

    // get most searched ads

    public function getMostViewed() {
        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()
        ->resultable()
        ->join( 'results', 'results.ad_id', '=', 'ads.id' )
        ->orderByDesc( 'results.view_count' )
        ->take( 20 )
        ->get();
    }

    // get most searched ads

    public function getMostShared() {
        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()
        ->resultable()
        ->join( 'results', 'results.ad_id', '=', 'ads.id' )
        ->orderByDesc( 'results.share_count' )
        ->take( 20 )
        ->get();
    }

    // get most favorited ads

    public function getMostFavorited() {
        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()
        ->resultable()
        ->join( 'results', 'results.ad_id', '=', 'ads.id' )
        ->orderByDesc( 'results.favorited_count' )
        ->take( 20 )
        ->get();
    }

    // get most call click ads

    public function getMostCallClick() {
        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()
        ->resultable()
        ->join( 'results', 'results.ad_id', '=', 'ads.id' )
        ->orderByDesc( 'results.call_click_count' )
        ->take( 20 )
        ->get();
    }

    // get most whatsapp click ads

    public function getMostWhatsappClick() {
        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()
        ->resultable()
        ->join( 'results', 'results.ad_id', '=', 'ads.id' )
        ->orderByDesc( 'results.whatsapp_click_count' )
        ->take( 20 )
        ->get();
    }

    // get most messages click ads

    public function getMostMessagesClick() {
        return Ad::with( [ 'category:id,name', 'tags' ] )
        ->owner()
        ->resultable()
        ->join( 'results', 'results.ad_id', '=', 'ads.id' )
        ->orderByDesc( 'results.messages_click_count' )
        ->take( 20 )
        ->get();
    }

    // increment share click count

    public function incrementShareCount( int $ad_id ) {
        return Results::increment( $ad_id, 'share_count' );
    }

    // increment call click count

    public function incrementCallClick( int $ad_id ) {
        return Results::increment( $ad_id, 'call_click_count' );
    }

    // increment whatsapp click count

    public function incrementWhatsappClick( int $ad_id ) {
        return Results::increment( $ad_id, 'whatsapp_click_count' );
    }

    // increment messages click count

    public function incrementMessagesClick( int $ad_id ) {
        return Results::increment( $ad_id, 'messages_click_count' );
    }

    public function resultMobileData( AdFilters $filters ) {

        $user_ads_count = request()->user()->ads()->count();
        $response = [
            'data' => []
        ];

        // filters

        // default filter active
        $active = $this->getActive( $filters );
        $active_count = count( $active );
        $response[ 'data' ] = [
            'rate' => $user_ads_count ? number_format( $active_count* 100 / $user_ads_count, 2 ) : 0.00,
            'count' => $active_count,
            'ads' => $active
        ];

        if ( request()->has( 'pending' ) ) {
            $pending = $this->getPending( $filters );
            $pending_count = count( $pending );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $pending_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $pending_count,
                'ads' => $pending
            ];

        } elseif ( request()->has( 'updateable' ) ) {
            $updateable = $this->getUpdateable();
            $updateable_count = count( $updateable );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $updateable_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $updateable_count,
                'ads' => $updateable
            ];

        } elseif ( request()->has( 'unaccept' ) ) {
            $unaccept = $this->getUnaccept();
            $unaccept_count = count( $unaccept );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $unaccept_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $unaccept_count,
                'ads' => $unaccept
            ];

        } elseif ( request()->has( 'almost_in' ) ) {
            $almost_in = $this->getAlmostInactive();
            $alomst_in_count = count( $almost_in );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $alomst_in_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $alomst_in_count,
                'ads' => $almost_in
            ];

        } elseif ( request()->has( 'inactive' ) ) {
            $inactive = $this->getInactive();
            $inactive_count = count( $inactive );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $inactive_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $inactive_count,
                'ads' => $inactive
            ];

        } elseif ( request()->has( 'ac_offers' ) ) {
            $active_offers = $this->getActiveOffers();
            $active_offers_count = count( $active_offers );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $active_offers_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $active_offers_count,
                'ads' => $active_offers
            ];

        } elseif ( request()->has( 'mo_search' ) ) {
            $most_searched = $this->getMostSearched();
            $most_searched_count = count( $most_searched );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $most_searched_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $most_searched_count,
                'ads' => $most_searched
            ];

        } elseif ( request()->has( 'mo_view' ) ) {
            $most_viewed = $this->getMostViewed();
            $most_viewed_count = count( $most_viewed );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $most_viewed_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $most_viewed_count,
                'ads' => $most_viewed
            ];

        } elseif ( request()->has( 'mo_share' ) ) {
            $most_shared = $this->getMostShared();
            $most_shared_count = count( $most_shared );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $most_shared_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $most_shared_count,
                'ads' => $most_shared
            ];

        } elseif ( request()->has( 'mo_favorite' ) ) {
            $most_favorited = $this->getMostFavorited();
            $most_favorited_count = count( $most_favorited );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $most_favorited_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $most_favorited_count,
                'ads' => $most_favorited
            ];

        } elseif ( request()->has( 'mo_call' ) ) {
            $most_call_click = $this->getMostCallClick();
            $most_call_click_count = count( $most_call_click );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $most_call_click_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $most_call_click_count,
                'ads' => $most_call_click
            ];

        } elseif ( request()->has( 'mo_whatsapp' ) ) {
            $most_whatsapp_click = $this->getMostWhatsappClick();
            $most_whatsapp_click_count = count( $most_whatsapp_click );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $most_whatsapp_click_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $most_whatsapp_click_count,
                'ads' => $most_whatsapp_click
            ];

        } elseif ( request()->has( 'mo_messages' ) ) {
            $most_messages_click = $this->getMostWhatsappClick();
            $most_messages_click_count = count( $most_messages_click );
            $response[ 'data' ] = [
                'rate' => $user_ads_count ? number_format( $most_messages_click_count* 100 / $user_ads_count, 2 ) : 0.00,
                'count' => $most_messages_click_count,
                'ads' => $most_messages_click
            ];
        }

        return response()->json( $response );
    }

    public function resultData( AdFilters $filters ) {
        return response()->json( [
            'active_ads'            => $this->getActive( $filters ),
            'pending_ads'           => $this->getPending( $filters ),
            'updateable_ads'        => $this->getUpdateable(),
            'unaccept_ads'          => $this->getUnAccept(),
            'almost_inactive'       => $this->getAlmostInactive(),
            'inactive_ads'          => $this->getInactive(),
            'active_offers'         => $this->getActiveOffers(),
            'most_searched'         => $this->getMostSearched(),
            'most_viewed'           => $this->getMostViewed(),
            'most_shared'           => $this->getMostShared(),
            'most_favorited'        => $this->getMostFavorited(),
            'most_call_click'       => $this->getMostCallClick(),
            'most_whatsapp_click'   => $this->getMostWhatsappClick(),
            'most_messages_click'   => $this->getMostMessagesClick(),
        ] );
    }
}
