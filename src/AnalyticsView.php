<?php

namespace Makeable\Analytics;

use Google_Service_Analytics;

class AnalyticsView
{
    use NormalizeParameters;

    protected $view_id;
    protected $account_id;
    protected $property_id;
    protected $name;
    protected $type;
    protected $created;

    public function __construct($viewData)
    {
        $this->view_id = $viewData->getId();
        $this->account_id = $viewData->getAccountId();
        $this->property_id = $viewData->getWebPropertyId();
        $this->name = $viewData->getName();
        $this->type = $viewData->getType();
        $this->created = $viewData->getCreated();
    }

    public static function all(AnalyticsUser $user, $account = null, $property = null)
    {
            $analytics = new Google_Service_Analytics($user->getClient());

        $response = $analytics->management_profiles
            ->listManagementProfiles(static::normalize($account), static::normalize($property));

        return collect($response->getItems())
          ->map(function ($view) {
              return new AnalyticsView($view);
          });
    }
}
