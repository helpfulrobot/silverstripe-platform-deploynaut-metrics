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
        // var_dump($data);
				$allmetrics = [];

				foreach ($data as $data) {

					$target = $data['target'];
        	//add target to metrics array
        	$metrics = array($target);

        	$timestamps = array('x');
        	//get amount of datapoints to use in loop
        	$ilimit = count($data['datapoints']);
        	//loop through datapoints and add to metrics array
        	for($i = 0; $i < $ilimit; $i++) {
            $metrics[] = $data['datapoints'][$i][0];
            $timestamps[] = $data['datapoints'][$i][1];
        	}

					array_push($allmetrics, [$timestamps, $metrics]);
				}
        // dd($timestamps);
				var_dump($allmetrics);
				// die();
        return json_encode($allmetrics);
    }

}
