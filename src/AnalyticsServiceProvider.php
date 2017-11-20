<?php

namespace Makeable\LaravelAnalytics;

use Google_Client;
use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AnalyticsClient::class, function () {
            return tap(new Google_Client, function ($client) {
                $client->setApplicationName(config('app.name'));
                $client->setAuthConfig([
                    'client_id' => config('services.google.oauth_client_id'),
                    'client_secret' => config('services.google.oauth_client_secret'),
                ]);
            });
        });
    }
}
