# Laravel Google Analytics
Fetches Analytics channel data based on oAuth

## Installation

```
composer require makeabledk/laravel-google-analytics
```

Add to your `AppServiceProvider@register` 
```php
$this->app->bind(AnalyticsClient::class, function () {
    return tap(new Google_Client, function ($client) {
        $client->setApplicationName(config('app.name'));
        $client->setAuthConfig([
            'client_id' => config('services.google.oauth_client_id'),
            'client_secret' => config('services.google.oauth_client_secret'),
        ]);
    });
});
```