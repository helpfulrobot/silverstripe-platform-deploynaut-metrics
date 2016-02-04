<?php

namespace DashboardMetrics;

class EnvironmentMetricsExtension extends \DataExtension {

  private static $db = array(
    'ShowMetrics' => 'Boolean',
  );

  private static $has_one = array(
    'MetricSet' => 'MetricSet',
  );

  private static $defaults = array(
    'ShowMetrics' => false
  );

  public function updateCMSFields(\FieldList $fields) {
    if (!$this->owner->Backend()->config()->supports_dashboard_metrics) return;

    foreach (\MetricSet::get() as $metricset) {
      $metricsetMap[$metricset->ID] = $metricset->Name;
    }

    $fields->addFieldsToTab('Root.Metrics', array(
      \CheckboxField::create('ShowMetrics', 'Display Metrics for this environment?'),
      \DropdownField::create('MetricSetID', 'Metric Set', $metricsetMap)
    ));
  }
}
