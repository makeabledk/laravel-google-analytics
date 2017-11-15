<?php

namespace Makeable\Analytics;

class AnalyticsUser
{

    protected $client;

    private function __construct()
    {
    }

    public static function find($refreshToken)
    {
        $cachekey = 'analytics_access_token_' . md5($refreshToken);
        /* @var \Google_Client $user->client */
        $user = new static;
        $user->client = app(AnalyticsClient::class);

        if (($cached = \Cache::get($cachekey)) !== null) {
            $user->client->setAccessToken($cached);
        }

        if ($user->client->isAccessTokenExpired()) {
            $token = $user->client->refreshToken($refreshToken);
            $user->client->setAccessToken($token);
            // Google oAuth tokens are available for 3600 seconds.
            \Cache::put($cachekey, $token, 55);
        }

        return $user;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getAccounts()
    {
        return AnalyticsAccount::all($this);
    }

    public function getProperties()
    {
        return AnalyticsProperty::all($this);
    }
}
