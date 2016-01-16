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
		'viewmetrics',
	);

	public function init() {
		parent::init();

		$project = $this->getCurrentProject();
		if (! $project) {
			return $this->project404Response();
		}

		if (! $project->allowed(Permissions::ALLOW_ENVIRONMENT_METRICS_READ)) {
			return \Security::permissionFailure();
		}

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
			'Environment' => $env,
			'Metrics' => $this->MetricSet()->metrics()
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

	public function Range() {

		$form = \DropdownField::create('TimeAgo', 'Hours of graphs to display', array(1, 2, 4, 8, 12, 24, 48));

		return $form->setEmptyString('Select a value...');
	}

	/**
	 * Output current data for a given metric
	 * @param Metric $metric The metric to return data for
	 * @todo  Clean this up _significantly_
	 */
	public function Data($metricID) {

		$ago = intval($this->getRequest()->getVar("timeago"));
		if (!is_numeric($ago) || !in_array($ago, array(1, 2, 4, 8, 12, 24, 48))) {
			$ago = 1;
		}

		$startTime = "-" . $ago . "hour";
		$metric = \Metric::get_by_id('Metric', $metricID);
		$project = $this->getCurrentProject();

		// If we are on the metrics root page, grab prod data
		$env = $this->getCurrentEnvironment($project);

		return $metric->query($env->RFCluster, $env->RFStack, $env->RFEnvironment, $startTime);

	}



}
