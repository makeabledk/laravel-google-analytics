<?php

namespace Makeable\LaravelAnalytics;

use AnalyticsWizard\Client;
use AnalyticsWizard\ReportRequest;
use Google_Service_Analytics;
use Google_Service_Analytics_Profile;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JsonSerializable;

class AnalyticsView implements Arrayable, JsonSerializable
{
    use HasAttributes,
        NormalizeParameters;

    /**
     * @var AnalyticsUser
     */
    protected $user;

    /**
     * AnalyticsView constructor.
     * @param AnalyticsUser $user
     * @param $data
     */
    public function __construct(AnalyticsUser $user, $data)
    {
        $this->user = $user;

        is_array($data) ? $this->fill($data) : $this->setAttributes($data);
    }

    /**
     * @param AnalyticsUser $user
     * @param null $account
     * @param null $property
     * @return Collection
     */
    public static function all(AnalyticsUser $user, $account = null, $property = null)
    {
        $analytics = new Google_Service_Analytics($user->getClient());

        $response = $analytics->management_profiles
            ->listManagementProfiles(static::normalize($account), static::normalize($property));

        return collect($response->getItems())
            ->map(function ($view) use ($user) {
                return new AnalyticsView($user, $view);
            });
    }

    /**
     * @param $callable
     * @return ReportRequest
     */
    public function query($callable)
    {
        return (new Client($this->user->getClient()))->fetchReport(
            tap(new ReportRequest($this->id), $callable)->get()
        );
    }

    /**
     * @param Google_Service_Analytics_Profile $data
     */
    protected function setAttributes(Google_Service_Analytics_Profile $data)
    {
        $this->id = $data->getId();
        $this->account_id = $data->getAccountId();
        $this->property_id = $data->getWebPropertyId();
        $this->name = $data->getName();
        $this->type = $data->getType();
        $this->created = $data->getCreated();
    }
}
