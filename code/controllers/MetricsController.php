<?php

namespace DashboardMetrics;

/**
 * Primary controller for the Metrics interface
 *
 * @package  deploynaut-metrics
 * @author  Garion Herman <garion@silverstripe.com>
 */
class MetricsController extends \DNRoot implements \PermissionProvider {

	const ACTION_METRICS = 'metrics';

	const ALLOW_ENVIRONMENT_METRICS_READ = 'ALLOW_ENVIRONMENT_METRICS_READ';

	public function providePermissions() {
		return array(
			self::ALLOW_ENVIRONMENT_METRICS_READ => array(
				'name' => "Read access to environment metrics",
				'category' => "Deploynaut",
			)
		);
	}

	public static $allowed_actions = array(
		'viewmetrics',
	);

	public function init() {

		parent::init();

		$project = $this->getCurrentProject();
		if (! $project ) {
			return $this->project404Response();
		}

		if (! $project->allowed(self::ALLOW_ENVIRONMENT_METRICS_READ)) {
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
		} else if (! $env->ShowMetrics) {
			return \Security::permissionFailure();
		}
		$metricset = MetricSet::get()->byID($this->getCurrentEnvironment()->MetricSetID);

		// Check for metrics actually being defined for env
		if (!$metricset || $metricset == NULL) {
			$metrics = false;
		} else {
			$metrics = $metricset->metrics();
		}

		return $this->customise(array(
			'Environment' => $env,
			'Metrics' => $metrics
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

	/**
	 * Sets Range for graphs to diplay via dropdown.
	 * @return DropdownField   The timepicker
	 */
	public function getRange() {
		$values = array(
			1 => "1 hour ago",
			2 => "2 hours ago",
			4 => "4 hours ago",
			8 => "8 hours ago",
			12 => "12 hours ago",
			24 => "24 hours ago",
			48 => "48 hours ago",
		);

		if (intval($this->getRequest()->getVar("timeago"))) {
			$currentvalue = (intval($this->getRequest()->getVar("timeago")));
			$field = \DropdownField::create(
				'TimeAgo',
				'Hours of graphs to display',
				$values,
				$currentvalue
			);
		} else {
			$field = \DropdownField::create(
				'TimeAgo',
				'Hours of graphs to display',
				$values
			);
		}
		return $field;
	}

	/**
	 * Output current data for a given metric
	 * @param int $metric The metric to return data for
	 * @todo  Clean this up _significantly_
	 */
	public function getData($metricID) {

		$ago = intval($this->getRequest()->getVar("timeago"));
		if (!is_numeric($ago) || !in_array($ago, array(1, 2, 4, 8, 12, 24, 48))) {
			$ago = 1;
		}

		$startTime = "-" . $ago . "hour";
		$metric = Metric::get()->byId($metricID);
		$project = $this->getCurrentProject();

		// If we are on the metrics root page, grab prod data
		$env = $this->getCurrentEnvironment($project);

		return $metric->query($env->RFCluster, $env->RFStack, $env->RFEnvironment, $startTime);
	}
}
