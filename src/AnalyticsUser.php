<?php

namespace Makeable\Analytics;

use Cache;
use Google_Client;
use Illuminate\Support\Collection;

class AnalyticsUser
{

    /**
     * @var Google_Client
     */
    protected $client;

    /**
     * @param $refreshToken
     * @return static
     */
    public static function find($refreshToken)
    {
        $cacheKey = 'analytics_access_token_' . md5($refreshToken);

        $user = new static;
        $user->client = app(AnalyticsClient::class);

        if (($cached = Cache::get($cacheKey)) !== null) {
            $user->client->setAccessToken($cached);
        }

        if ($user->client->isAccessTokenExpired()) {
            $token = $user->client->refreshToken($refreshToken);
            $user->client->setAccessToken($token);

            // Google oAuth tokens are available for 3600 seconds.
            Cache::put($cacheKey, $token, 55);
        }

        return $user;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return Collection
     */
    public function getAccounts()
    {
        return AnalyticsAccount::all($this);
    }

    /**
     * @return Collection
     */
    public function getProperties()
    {
        return AnalyticsProperty::all($this);
    }
}
