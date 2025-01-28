<?php

namespace App\Jobs\Api\V1;

use App\Models\Api\V1\Ad;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckExpiredAds implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
    * Create a new job instance.
    */

    public function __construct() {
        //
    }

    /**
    * Execute the job.
    */

    public function handle() : void{
        // get all active ads
        $ads = Ad::with( 'user' )->where( 'status', 'active' )->get();
        foreach ( $ads as $ad ) {
            // get user subscription
            $subscription = $ad->user->subscription;

            if ( $ad->type == 'sale' ) {
                // check sale ads validity
                if ($ad->created_at->addDays( $subscription->sale_ads_validity ) <= now() ) {
                    $ad->update( [
                        'status' => 'inactive',
                    ] );
                }
            } else {
                // check buy ads validity
                if ( $ad->created_at->addDays( $subscription->buy_ads_validity ) <= now() ) {
                    $ad->update( [
                        'status' => 'inactive',
                    ] );
                }
            }
        }
    }
}
