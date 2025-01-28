<?php

namespace App\Jobs\Api\V1;

use App\Models\Api\V1\Package;
use App\Models\Api\V1\User;
use App\Notifications\Api\V1\DatabaseUserNotification;
use App\Traits\Api\V1\UpdateUserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckExpiredSubscription implements ShouldQueue {
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
        // get all users
        $users = User::with( 'subscription' )->whereRelation( 'subscription', 'package_id', '<>', 1 )->get();
        // get default backage name
        $default_package = Package::where( 'id', 1 )->first();
        foreach ( $users as $user ) {
            // get current user subscription
            $subscription = $user->subscription;
            $package = $subscription->package;
            // check current packege validity
            if ( $subscription->created_at->format( 'd' ) <= now()->subDays( $package->validity )->format( 'd' ) ) {
                // update user subscription to the default package
                if ( UpdateUserSubscription::updateOrCreate( $default_package, $subscription ) ) {
                    // send notification to the user
                    $message = 'عزيزي المستخدم نود إعلامك انه قد إنتهى إشتراكك بباقة ' . $package->name . ' علماً انه تم تفعيل إشتراكك بباقة ' . $default_package->name;
                    $user->notify( new DatabaseUserNotification( $message, 'نظام الإشتراكات', 'إنتهاء صلاحية الإشتراك' ) );
                }
            }
        }
    }
}
