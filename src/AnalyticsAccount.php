<?php

namespace Makeable\Analytics;


use Google_Service_Analytics;


class AnalyticsAccount
{
    protected $id;
    protected $name;
    protected $created;
    protected $user;
    public $properties;

    public function __construct($accountData)
    {
        $this->id = $accountData->id;
        $this->name = $accountData->name;
        $this->created = $accountData->created;
    }

    public static function all(AnalyticsUser $user, $load = null) // properties.views
    {
        $analytics = new Google_Service_Analytics($user->getClient());

        try {
            $response = $analytics->management_accounts->listManagementAccounts();
        } catch (apiServiceException $e) {
            throw new \Exception(
              'There was an Analytics API service error '
              . $e->getCode() . ':' . $e->getMessage()
            );

        } catch (apiException $e) {
            throw new \Exception(
              'There was a general API error '
              . $e->getCode() . ':' . $e->getMessage()
            );
        }

        return collect($response->getItems())
          ->map(function ($account) use ($load, $user) {
              return tap(new AnalyticsAccount($account), function($account) use ($load, $user) {
                  $account->user = $user;

                  if ($load) {
                      $account->properties = $account->getProperties($load);
                  }
              });
          });
    }
    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getProperties($load)
    {
        return AnalyticsProperty::all($this, $load);
    }
}
