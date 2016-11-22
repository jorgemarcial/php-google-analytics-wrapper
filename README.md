# php-google-analytics-wrapper

### Example of use code

```php


$keyFileJson = 'service-account-credentials.json';

// Create and configure a new client object.
$report = new Report(
    array(
        'keyFileLocation' => $keyFileJson,
        'applicationName' => 'Example Analytics Reporting'
    )
);

// set date range with from and to dates.
$dateRange = $report->setDateRange('YYYY-MM-DD', 'YYYY-MM-DD');
// set metrics
$sessionsMetric = $report->setMetric("ga:sessions", "sessions");
// set dimensions
$operatingSystemDimension = $report->setDimension("ga:operatingSystem");

// generate reports object
$reports = $report->getReports(
    "VIEW_ID", 
    $dateRange,
    [$sessionsMetric],
    [$countryDimension]
);

// get array with results
$results = $report->getResults($reports);
```