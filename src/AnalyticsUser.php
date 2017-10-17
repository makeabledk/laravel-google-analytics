<?php

namespace Makeable\Analytics;

use Google_Service_Analytics;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_ReportRequest;
use Illuminate\Support\Collection;

class AnalyticsUser
{

    protected $client;

    private function __construct()
    {
    }

    public static function find($refreshToken)
    {
        $cachekey = 'analytics_access_token_'.md5($refreshToken);

        /* @var \Google_Client $user->client */
        $user = new static;
        $user->client = app(AnalyticsClient::class);

        if (($cached = \Cache::get($cachekey)) !== null) {
            $user->client->setAccessToken($cached);
        }

        if ($user->client->isAccessTokenExpired()) {
            $token = $user->client->refreshToken($refreshToken);
            $user->client->setAccessToken($token);
            // Google oAuth tokens are available for 3600 seconds.
            \Cache::put($cachekey, $token, 55);
        }

        return $user;
    }
    public function getClient()
    {
        return $this->client;
    }

    public function getAccounts()
    {
        return AnalyticsAccount::all($this, true);
    }



    public function getReport()
    {
        $analytics = new Google_Service_AnalyticsReporting($this->getClient());

        //dd($analytics);

        // Replace with your view ID, for example XXXX.
        $VIEW_ID = "153944849";

        // Create the DateRange object.
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate("3000daysAgo");
        $dateRange->setEndDate("today");

        // Create the Metrics object.
        $sessions = new Google_Service_AnalyticsReporting_Metric();
        $sessions->setExpression("ga:sessions");
        $sessions->setAlias("sessions");

        $views = new Google_Service_AnalyticsReporting_Metric();
        $views->setExpression("ga:pageviews");
        $views->setAlias("pageviews");

        // Create the ReportRequest object.
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($VIEW_ID);
        $request->setDateRanges($dateRange);
        $request->setMetrics(array($sessions, $views));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $request) );

        return $analytics->reports->batchGet( $body );

    }

    /**
     * Parses and prints the Analytics Reporting API V4 response.
     *
     * @param An Analytics Reporting API V4 response.
     */
    public function printResults($reports) {
        for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
            $report = $reports[ $reportIndex ];
            $header = $report->getColumnHeader();
            $dimensionHeaders = $header->getDimensions();
            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
            $rows = $report->getData()->getRows();

            for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                $row = $rows[ $rowIndex ];
                $dimensions = $row->getDimensions();
                $metrics = $row->getMetrics();
                for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
                    print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
                }

                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        $entry = $metricHeaders[$k];
                        print($entry->getName() . ": " . $values[$k] . "\n");
                    }
                }
            }
        }
    }

}
