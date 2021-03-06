<?php

namespace DashboardMetrics;

class EnvironmentMetricsExtension extends \DataExtension {

  private static $db = array(
    'ShowMetrics' => 'Boolean',
  );

  private static $has_one = array(
    'MetricSet' => 'DashboardMetrics\MetricSet',
  );

  private static $defaults = array(
    'ShowMetrics' => false
  );

  public function updateCMSFields(\FieldList $fields) {
    if (!$this->owner->Backend()->config()->supports_dashboard_metrics) return;

    $fields->addFieldsToTab('Root.Metrics', array(
      \CheckboxField::create('ShowMetrics', 'Display Metrics for this environment?'),
      \DropdownField::create('MetricSetID', 'Metric Set', MetricSet::get()->map())
    ));
  }
}
