<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Instagram\InstagramExtendSocialite;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

public function boot()
{
    $this->app->make('events')->listen(SocialiteWasCalled::class, function ($socialiteWasCalled) {
        $socialiteWasCalled->extendSocialite('instagram', InstagramExtendSocialite::class);
    });
}

}
