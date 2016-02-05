<?php

class MetricsAdmin extends ModelAdmin {

	private static $managed_models = array(
		'DashboardMetrics\Metric',
		'DashboardMetrics\MetricSet'
	);

	private static $url_segment = 'metrics';

	private static $menu_title = 'Metrics';
}