<?php

namespace App\Jobs\Api\V1;

use App\Models\Api\V1\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckHideOfferValue implements ShouldQueue {
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
        // get all active users
        $users = User::where( 'status', 'active' )->with( 'offers' )
        ->whereRelation( 'offers', 'type', '=', 'hidden' )->get();

        foreach ( $users as $user ) {
            // get user offers
            $offers = $user->offers;
            foreach ( $offers as $offer ) {
                // check user offer validity for hidden
                if ( $offer->created_at->addDays( 7 )->format( 'd' ) <= now()->format( 'd' ) ) {
                    $offer->update( [
                        'type' => 'visible'
                    ] );
                }
            }
        }
    }
}
