<?php
/**
 * Created by PhpStorm.
 * User: troels
 * Date: 17/10/2017
 * Time: 15.04
 */

namespace Makeable\Analytics;


use Google_Service_Analytics;

class AnalyticsView
{
    protected $id;
    protected $name;
    protected $type;
    protected $created;


    public function __construct($viewData)
    {
        var_dump('<pre>');
        var_dump($viewData);
        $this->id = $viewData->getId();
        $this->name = $viewData->getName();
        $this->type = $viewData->getType();
        $this->created = $viewData->getCreated();
    }

    public static function all(AnalyticsProperty $property, AnalyticsUser $user, AnalyticsAccount $account)
    {

        $analytics = new Google_Service_Analytics($user->getClient());

        try {
            $response = $analytics->management_profiles
             // ->listManagementProfiles($account->getId(), $property->getId());
              ->listManagementProfiles('~all', '~all');

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
          ->map(function ($view) {
              return new AnalyticsView($view);
          });

    }
}
