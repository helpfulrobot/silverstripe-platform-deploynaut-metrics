<?php

namespace DashboardMetrics;

/**
 * Primary controller for the Metrics interface
 *
 * @package  deploynaut-rainforest-metrics
 * @author  Garion Herman <garion@silverstripe.com>
 */
class Dispatcher extends \DNRoot {

	const ACTION_METRICS = 'metrics';

	public static $allowed_actions = array(
		'viewmetrics'
	);

	public function init() {
		parent::init();

		$project = $this->getCurrentProject();
		if (! $project) {
			return $this->project404Response();
		}

		// TODO: Permission checks go here
	}

	public function index(\SS_HTTPRequest $request) {
		$this->setCurrentActionType(self::ACTION_METRICS);

		$project = $this->getCurrentProject();
		if (! $project) {
			return $this->project404Response();
		}

		$env = $this->getCurrentEnvironment($project);
		if (! $env) {
			return $this->environment404Response();
		}

		return $this->customise(array(
			'Environment' => $env
		))->renderWith(array('DashboardMetrics_metrics', 'DNRoot'));
	}


	/**
	 * Displays metrics for a project.
	 * @param  SS_HTTPRequest  $request The request object
	 * @return SS_HTTPResponse          The response object
	 * @todo   Clean up DB logic
	 */
	public function viewmetrics(\SS_HTTPRequest $request) {

		return $this->render();

	}

	public function MetricSet() {
		// TODO: Check if there's an alternative default for this project

		$metricSet = \MetricSet::get()->filter(array(
			'Enabled' => true
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
		$metric = \Metric::get_by_id('Metric', $metricID);
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