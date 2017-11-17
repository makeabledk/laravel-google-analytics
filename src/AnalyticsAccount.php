<?php

namespace Makeable\Analytics;

use Google_Service_Analytics;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JsonSerializable;

class AnalyticsAccount implements Arrayable, JsonSerializable
{
    use HasAttributes;

    /**
     * @var
     */
    protected $user;

    /**
     * AnalyticsAccount constructor.
     * @param $accountData
     */
    public function __construct($accountData)
    {
        $this->id = $accountData->id;
        $this->name = $accountData->name;
        $this->created = $accountData->created;
    }

    /**
     * @param AnalyticsUser $user
     * @return Collection
     */
    public static function all(AnalyticsUser $user)
    {
        $analytics = new Google_Service_Analytics($user->getClient());
        $response = $analytics->management_accounts->listManagementAccounts();

        return collect($response->getItems())
          ->map(function ($account) {
              return new AnalyticsAccount($account);
          });
    }
}
