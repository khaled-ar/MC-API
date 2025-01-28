<?php

namespace App\Console;

use App\Jobs\Api\V1\CheckExpiredAds;
use App\Jobs\Api\V1\CheckExpiredSubscription;
use App\Jobs\Api\V1\CheckHideOfferValue;
use App\Jobs\Api\V1\CheckHighlightingOffers;
use App\Jobs\Api\V1\CheckPackageDiscount;
use App\Jobs\Api\V1\CheckPinableAds;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // Check Expired Subscriptions
        $schedule->job(new CheckExpiredSubscription())->dailyAt('21:00');
        // Check Expired Ads
        $schedule->job(new CheckExpiredAds())->dailyAt('21:03');
        // Check Pinable ads
        $schedule->job(new CheckPinableAds())->dailyAt('21:07');
        // Check Hidden Offers
        $schedule->job(new CheckHideOfferValue())->dailyAt('21:11');
        // Check Highlightin Offers
        $schedule->job(new CheckHighlightingOffers())->dailyAt('21:15');
        // Check Package Discount
        $schedule->job(new CheckPackageDiscount())->dailyAt('21:20');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
