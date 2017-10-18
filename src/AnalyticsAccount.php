<?php

namespace Makeable\Analytics;

use Google_Service_Analytics;

class AnalyticsAccount
{
    protected $user;
    protected $account_id;
    protected $name;
    protected $created;

    public function __construct($accountData)
    {
        $this->account_id = $accountData->id;
        $this->name = $accountData->name;
        $this->created = $accountData->created;
    }

    public static function all(AnalyticsUser $user)
    {
        $analytics = new Google_Service_Analytics($user->getClient());
        $response = $analytics->management_accounts->listManagementAccounts();

        return collect($response->getItems())
          ->map(function ($account) {
              return new AnalyticsAccount($account);
          });
    }
    public function getId()
    {
        return $this->account_id;
    }
}
