<?php

namespace Makeable\LaravelAnalytics;

use Google_Service_Analytics;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JsonSerializable;

class AnalyticsProperty implements Arrayable, JsonSerializable
{
    use HasAttributes,
        NormalizeParameters;

    /**
     * @var AnalyticsUser
     */
    protected $user;

    /**
     * AnalyticsProperty constructor.
     * @param AnalyticsUser $user
     * @param $propertyData
     */
    public function __construct(AnalyticsUser $user, $propertyData)
    {
        $this->user = $user;

        $this->id = $propertyData->getId();
        $this->account_id = $propertyData->getAccountId();
        $this->name = $propertyData->getName();
        $this->url = $propertyData->getWebsiteUrl();
        $this->created = $propertyData->getCreated();
    }

    /**
     * @param AnalyticsUser $user
     * @param null $account
     * @return Collection
     */
    public static function all(AnalyticsUser $user, $account = null)
    {
        $analytics = new Google_Service_Analytics($user->getClient());

        $response = $analytics->management_webproperties
            ->listManagementWebproperties(static::normalize($account));

        return collect($response->getItems())
            ->map(function ($property) use ($user) {
                return new AnalyticsProperty($user, $property);
            });
    }

    /**
     * @return Collection
     */
    public function getViews()
    {
        return AnalyticsView::all($this->user, $this->account_id, $this->id);
    }
}
