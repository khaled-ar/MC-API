<?php

namespace App\Jobs\Api\V1;

use App\Models\Api\V1\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckPackageDiscount implements ShouldQueue {
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
        $packages = Package::where( 'discount', 1 )->get();
        foreach ( $packages as $package ) {
            if ( $package->created_at->addDays( 5 )->format( 'd' ) <= now()->format( 'd' ) ) {
                $package->update( [
                    'discount' => 0
                ] );
            }
        }
    }
}
