<?php

namespace Makeable\Analytics;

use Google_Service_Analytics;

class AnalyticsProperty
{
    use NormalizeParameters;

    protected $account_id;
    protected $property_id;
    protected $name;
    protected $url;
    protected $created;
    protected $user;

    public function __construct(AnalyticsUser $user, $propertyData)
    {
        $this->user = $user;
        $this->account_id = $propertyData->getAccountId();
        $this->property_id = $propertyData->getId();
        $this->name = $propertyData->getName();
        $this->url = $propertyData->getWebsiteUrl();
        $this->created = $propertyData->getCreated();
    }

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
    public function getViews($user, $account)
    {
        return AnalyticsView::all($user, $account);
    }
    public function getId()
    {
        return $this->property_id;
    }
}
