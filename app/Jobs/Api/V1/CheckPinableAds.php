<?php

namespace App\Jobs\Api\V1;

use App\Models\Api\V1\Ad;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckPinableAds implements ShouldQueue {
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

    public function handle(): void {
        // get all active pinable buy ads
        $buy_ads = Ad::where( 'type', 'buy' )->where( 'status', 'active' )->where( 'pinable', 1 )->get();
        foreach ( $buy_ads as $ad ) {
            // check pinable validity
            if ( $ad->created_at->addDays( 7 )->format( 'd' ) <= now()->format( 'd' ) ) {
                $ad->update( [
                    'pinable' => 0,
                ] );
            }
        }

        // get all active pinable sale ads
        $sale_ads = Ad::where( 'type', 'sale' )->where( 'status', 'active' )->where( 'pinable', 1 )->get();
        foreach ( $sale_ads as $ad ) {
            // get sale ads pinable validity
            $pinable_validity = $ad->user->subscription->package->pinable_validity;
            // check pinable validity
            if ( $ad->created_at->addDays( $pinable_validity )->format( 'd' ) <= now()->format( 'd' ) ) {
                $ad->update( [
                    'pinable' => 0,
                ] );
            }
        }
    }
}
