<?php

namespace DashboardMetrics;

use \GuzzleHttp\Client as GuzzleClient;
use \GuzzleHttp\Exception\CurlException as GuzzleCurlException;
use \GuzzleHttp\Exception\ServerException as GuzzleServerException;

/**
 * Metric current serves two purposes:
 *  - Defines the model for individual metrics
 *  - Performs queries to Graphite
 *
 * This needs to be refactored to extract the API interactions
 * into a Service, but will serve purpose for MVP.
 */
class Metric extends \DataObject {

    private static $db = array(
        'Name' => 'Varchar(100)',
        'Description' => 'Text',
        'Query' => 'Varchar(1000)',
    );

    private static $belongs_many_many = array(
        'MetricSets' => 'DashboardMetrics\MetricSet'
    );

    /**
     * Replaces query variables with Environment data
     *
     * @param  string $cluster     The cluster the environment is in
     * @param  string $stack       The stack the environment is in
     * @param  string $environment The name of the environment
     * @return string              A query string with the C/S/E added
     *
     * @todo   Utilise grammar parsing to make this more flexible
     */
    public function parse($cluster, $stack, $environment) {
        $parsedString = '';

        // split out separate targets
        $targets = explode(';', $this->Query);

        foreach ($targets as $target) {
            $target = str_replace('{cluster}', $cluster, $target);
            $target = str_replace('{stack}', $stack, $target);
            $target = str_replace('{env}', $environment, $target);

            $parsedString .= '&target=' . $target;
        }
        return $parsedString;
    }

    /**
     * Makes a query to Graphite and returns formatted data
     *
     * @param  string $cluster       The cluster the environment is in
     * @param  string $stack         The stack the environment is in
     * @param  string $environment   The environment
     * @param  string $startTime     Either relative (-1hour, etc.) or absolute (12:59_20151003)
     * @param  string $endTime       Either relative (-1hour, etc.) or absolute (12:59_20151003)
     * @param  int    $maxDataPoints The maximum number of datapoints that should be returned.
     * @return string                JSON-formatted metric data
     *
     * @todo   Return intelligent error information on failures instead of a blank JSON array
     * @todo   Extract into a service
     */
    public function query($cluster, $stack, $environment, $startTime = '-1hour', $endTime = 'now', $maxDataPoints = 120) {
        $url = 'http://metrics.platform.silverstripe.com/render?format=json';

        // Timestamps & Granularity
        $url .= '&from=' . $startTime;
        $url .= '&until=' . $endTime;
        $url .= '&maxDataPoints=' . $maxDataPoints;

        $url .= $this->parse($cluster, $stack, $environment);

        $client = new GuzzleClient([
            'timeout' => 5,
            'connect_timeout' => 1
        ]);

        try {
            $request = $client->get($url);
            $data = $request->json();
        } catch (GuzzleCurlException $e) {
            // Something went wrong with the request (probably no access to the Graphite server?)
            SS_Log::log('Metrics Request Failure: '. $e->getMessage(), SS_Log::WARN);
            return json_encode([]);
        } catch (GuzzleServerException $e) {
            // Graphite threw a hissy fit (probably malformed query)
            SS_Log::log('Metrics Query Failure: '. $e->getMessage(), SS_Log::WARN);
            return json_encode([]);
        }

        $final = array();
        $timestamps = array();

        foreach ($data as $q => $query) {
            $points = array();

            //loop through datapoints and add to metrics array
            foreach ($query['datapoints'] as $point) {
                $points[] = $point[0];

                // Grab timestamps from first query
                if ($q == 0) {
                    $timestamps[] = $point[1];
                }
            }

            $final[] = array(
                'name' => $query['target'],
                'color' => '',
                'data' => $points
            );
        }

        // Add timestamps
        $final[] = array(
            'name' => 'x',
            'data' => $timestamps
        );

        return json_encode($final);
    }

}
