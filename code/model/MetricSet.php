<?php

namespace DashboardMetrics;

class MetricSet extends \DataObject {

	private static $db = array(
		'Name' => 'Varchar(100)',
		'Enabled' => 'Boolean'
	);

	private static $has_many = array(
		'Environments' => 'DNEnvironment',
	);

	private static $many_many = array(
		'Metrics' => 'DashboardMetrics\Metric'
	);

	private static $defaults = array(
		'Enabled' => true
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$metricsMap = array();

		foreach (Metric::get() as $metric) {
			$metricsMap[$metric->ID] = $metric->Name;
		}

		$metricsField = \ListboxField::create('Metrics', 'Metrics')
			->setMultiple(true)
			->setSource($metricsMap);

		$fields->addFieldToTab('Root.Main', $metricsField);

		return $fields;
	}
}
