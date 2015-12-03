<?php

/**
 * Primary controller for the Metrics interface
 *
 * @package  deploynaut-rainforest-metrics
 * @author  Garion Herman <garion@silverstripe.com>
 */
class MetricsController extends DNRoot {

	public static $url_handlers = array(
		'$Project!/metrics/$Environment' => 'viewmetrics',
	);

	public static $allowed_actions = array(
		'viewmetrics'
	);

	/**
	 * Displays metrics for a project.
	 * @param  SS_HTTPRequest  $request The request object
	 * @return SS_HTTPResponse          The response object
	 * @todo   Clean up DB logic
	 */
	public function viewmetrics(SS_HTTPRequest $request) {

		// debug eet
		// dd($request);
// dd($this->getCurrentProject());


		$metricResults = [];

		// TODO: Pseudo-code
		// foreach($metricSet->Metrics() as $metric) {
		// 	$metricResults[] = $metric->query($cluster, $stack, $environment, '-2hours', 'now');
		// }

		// $metricResults = new ArrayData(['Metrics' => $metricResults]);

		// return $this->customise([
		// 	'Title' => 'IT ME, METRICK',
		// 	'MetricsData' => $metricResults
		// ])->render();
		
		// var_dump($this->templates);
		// die();
		//var_dump(new ArrayData(array('MetricSet' => $metricSet)));
		return $this->render();
	}

	public function MetricSet() {

		$metricSet = MetricSet::get()->filter(array(
			'Name' => 'Primary Metrics'
		))->first();

		return $metricSet;
	}

	/**
	 * Output current data for a given metric
	 * @param Metric $metric The metric to return data for
	 * @todo  Clean this up _significantly_
	 */
	public function Data($metricID) {

		$environmentName = $this->urlParams['Environment'];
		$metric = Metric::get_by_id('Metric', $metricID);
		$project = $this->getCurrentProject();

		// If we are on the metrics root page, grab prod data
		if (is_null($environmentName)) {
			$env = $project->DNEnvironmentList()
					->filter('Usage', 'Production')
					->First();

			if (! $env) {
				$env = $project->DNEnvironmentList()
						->filter('Usage', 'UAT')
						->First();
			}
		}
		
		return $metric->query($env->RFCluster, $env->RFStack, $env->RFEnvironment);

	}

}