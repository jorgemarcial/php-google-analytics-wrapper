<?php

namespace Jorgemarcial\GoogleAnalytics\AnalyticsReporting;

/**
 * Class Report
 */
class Report
{
    /** @var Analitcs Reporting Class */
    protected $analytics;

    /**
     * Set analytics Google_Service_AnalyticsReporting object
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $config = array_merge([
            'keyFileLocation' => null,
            'applicationName' => 'Gigigo Analytics Reporting',
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly'
        ], $config);

        $this->analytics = $this->initializeAnalytics($config);
    }

    /**
     * initializeAnalytics with parameters
     *
     * @param $config config parameters
     *
     * @return $analytics Analytics Object
     */
    public function initializeAnalytics($config)
    {
        // Create and configure a new client object.
        $client = new \Google_Client();

        $client->setApplicationName($config['applicationName']);
        $client->setAuthConfig($config['keyFileLocation']);
        $client->setScopes($config['scope']);
        $analytics = new \Google_Service_AnalyticsReporting($client);

        return $analytics;
    }

    /**
     *
     * @param dateFrom
     * @param dateTo
     *
     * @return $dateRange Google_Service_AnalyticsReporting_DateRange
     */
    public function setDateRange($dateFrom, $dateTo)
    {
        // Create the DateRange object.
        $dateRange = new \Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($dateFrom);
        $dateRange->setEndDate($dateTo);

        return $dateRange;
    }

    /**
     *
     * @param $expresion
     * @param $alias
     *
     * @return $metric Google_Service_AnalyticsReporting_Metric
     */
    public function setMetric($expression, $alias)
    {
        // Create the Metrics object.
        $metric = new \Google_Service_AnalyticsReporting_Metric();
        $metric->setExpression($expression);
        $metric->setAlias($alias);

        return $metric;
    }

    /**
     *
     */
    public function setDimension($expression)
    {
        $dimension = new \Google_Service_AnalyticsReporting_Dimension();
        $dimension->setName($expression);

        return $dimension;
    }

    /**
     * GetReport Object
     *
     * @param view View Id
     * @param dateRange Google_Service_AnalyticsReporting_DateRange Object
     * @param metrics array of Google_Service_AnalyticsReporting_Metric Object
     * @param dimensions array of Google_Service_AnalyticsReporting_Dimension Object
     *
     * @return reports Object
     */
    public function getReports($viewId, $dateRange = null, array $metrics = null, array $dimensions = null)
    {

        // Create the Rep1ortRequest object.
        $request = new \Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($viewId);

        $request->setDateRanges($dateRange);
        $request->setMetrics($metrics);
        $request->setDimensions($dimensions);

        $body = new \Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $request) );

        return $this->analytics->reports->batchGet( $body );
    }

    /**
     * Get Results
     *
     * @param $reports Object
     *
     * @return array all results
     */
    public function getResults($reports)
    {
        $results = [];

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
                    $results[$rowIndex][$dimensionHeaders[$i]] = $dimensions[$i];
                }
                for ($j = 0; $j < count( $metricHeaders ) && $j < count( $metrics ); $j++) {
                    $entry = $metricHeaders[$j];
                    $values = $metrics[$j];
                    for ( $valueIndex = 0; $valueIndex < count( $values->getValues() ); $valueIndex++ ) {
                        $entry = $metricHeaders[$valueIndex];
                        $value = $values->getValues()[ $valueIndex ];
                        $results[$rowIndex][$entry->getName()] = $value;
                    }
                }
            }
        }

        return $results;
    }
}
