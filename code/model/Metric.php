<?php

class Metric extends DataObject {

    private static $db = array(
        'Name' => 'Varchar(100)',
        'Description' => 'Text',
        'Query' => 'Varchar(1000)',
    );

    private static $belongs_many_many = array(
        'MetricSets' => 'MetricSet'
    );

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
     * @param  string $cluster     The cluster the environment is in
     * @param  string $stack       The stack the environment is in
     * @param  string $environment The environment
     * @param  string $startTime   Either relative (-1hour, etc.) or absolute (12:59_20151003)
     * @param  string $endTime     Either relative (-1hour, etc.) or absolute (12:59_20151003)
     * @return string              JASON-formatted metrics
     * @todo   Make this code less trashy
     * @todo   Handle failed API calls to Graphite gracefully
     * @todo   Extract into a service
     */
    public function query($cluster, $stack, $environment, $startTime = '-1hour', $endTime = 'now') {
        $url = 'http://metrics.platform.silverstripe.com/render?format=json';

        // Timestamps
        $url .= '&from=' . $startTime;
        $url .= '&until=' . $endTime;

        $url .= $this->parse($cluster, $stack, $environment);

        $client = new GuzzleHttp\Client([
            'timeout' => 5,
            'connect_timeout' => 1
        ]);

        $request = $client->get($url);

        $data = $request->json();
        $final = array();
        $timestamps = array();

        foreach ($data as $q => $query) {
            $points = array();

            //loop through datapoints and add to metrics array
            for ($i = 0; $i < count($query['datapoints']); $i++) {
                $points[] = $query['datapoints'][$i][0];

                // Grab timestamps from first query
                if ($q == 0) {
                    $timestamps[] = $query['datapoints'][$i][1];
                }
            }

            array_push($final, array(
                'name' => $query['target'],
                'color' => '',
                'data' => $points
            ));
        }

        // Add timestamps
        array_push($final, array(
            'name' => 'x',
            'data' => $timestamps
        ));

        return json_encode($final);
    }

}
