<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
    * Register any application services.
    */

    public function register(): void {
        parent::register();
        $this->app->bind( 'permissions', fn() => include base_path( '/data/permissins.php' ) );
    }

    /**
    * Bootstrap any application services.
    */

    public function boot(): void {
        //
    }
}
