<?php
/**
 * Created by PhpStorm.
 * User: troels
 * Date: 17/10/2017
 * Time: 14.28
 */

namespace Makeable\Analytics;


use Google_Service_Analytics;

class AnalyticsProperty
{
    protected $id;
    protected $name;
    protected $url;
    protected $created;

    public function __construct($propertyData)
    {
        $this->id = $propertyData->getId();
        $this->name = $propertyData->getName();
        $this->url = $propertyData->getWebsiteUrl();
        $this->created = $propertyData->getCreated();
    }

    public static function all(AnalyticsAccount $account)
    {
        $analytics = new Google_Service_Analytics($account->getUser()->getClient());

        try {
            $response = $analytics->management_webproperties
              ->listManagementWebproperties($account->getId());

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
          ->map(function ($property) {
              return new AnalyticsProperty($property);
          });
    }
    public function getViews($user, $account)
    {
        return AnalyticsView::all($this, $user, $account);
    }
    public function getId()
    {
        return $this->id;
    }
}
