<?php

namespace App\Traits\Api\V1;

use App\Models\Api\V1\Package;
use App\Models\Api\V1\Subscription;
use App\Notifications\Api\V1\DatabaseUserNotification;
use Illuminate\Support\Facades\DB;

trait UpdateUserSubscription {

    // this function to update user subscription
    public static function updateOrCreate( Package $new_package, Subscription $old_subscription ) {

        try {
            if ( $old_subscription->package_id != 1 && $new_package->id != 1 ) {

                // update user subscription
                $new_subscription = DB::table( 'subscriptions' )->where( 'id', $old_subscription->id )
                ->update( [
                    'package_id'        => $new_package->id,
                    'sale_ads_validity' => DB::raw( "sale_ads_validity + {$new_package->sale_ads_validity}" ),
                    'sale_ads_limit'    => DB::raw( "sale_ads_limit + {$new_package->sale_ads_limit}" ),
                    'buy_ads_validity'  => DB::raw( "buy_ads_validity + {$new_package->buy_ads_validity}" ),
                    'buy_ads_limit'     => DB::raw( "buy_ads_limit + {$new_package->buy_ads_limit}" ),
                    'offers_limit'      => DB::raw( "offers_limit + {$new_package->offers_limit}" ),
                    'service_discounts' => DB::raw( "service_discounts + {$new_package->service_discounts}" ),

                ] );

            } else {

                // update user subscription to the defualt package
                $new_subscription = DB::table( 'subscriptions' )->where( 'id', $old_subscription->id )
                ->update( [
                    'package_id'        => $new_package->id,
                    'sale_ads_validity' => $new_package->sale_ads_validity,
                    'sale_ads_limit'    => $new_package->sale_ads_limit,
                    'buy_ads_validity'  => $new_package->buy_ads_validity,
                    'buy_ads_limit'     => $new_package->buy_ads_limit,
                    'offers_limit'      => $new_package->offers_limit,
                    'service_discounts' => $new_package->service_discounts,
                ] );

            }

        } catch ( \Throwable ) {
            $new_subscription = 0;
        }

        return $new_subscription != 0;
    }

    // this function to active default subsicribtion for the user
    public static function defaultSubsicribe( $user ) {
        // get default package
        $defaultPackage = Package::firstWhere( 'id', 1 );

        if ( ! $defaultPackage ) {
            $user->notify( new DatabaseUserNotification( 'حدث خطأ ما، لم يتم الاشتراك بالباقة الافتراضية', 'نظام الإشتراكات', 'الباقة الافتراضية' ) );
            return;
        }

        // active default subsicribtion
        Subscription::create( [
            'user_id' => $user->id,
            'package_id' => 1,
            'sale_ads_validity' => $defaultPackage->sale_ads_validity,
            'sale_ads_limit'    => $defaultPackage->sale_ads_limit,
            'buy_ads_validity'  => $defaultPackage->buy_ads_validity,
            'buy_ads_limit'     => $defaultPackage->buy_ads_limit,
            'offers_limit'      => $defaultPackage->offers_limit,
            'service_discounts' => $defaultPackage->service_discounts,
        ] );
        $user->notify( new DatabaseUserNotification( 'تم الاشتراك بالباقة الافتراضية', 'نظام الإشتراكات', 'الباقة الافتراضية' ) );
    }
}
