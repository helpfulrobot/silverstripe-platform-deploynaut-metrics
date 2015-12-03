<?php

class MetricsAdmin extends ModelAdmin {

	private static $managed_models = array(
		'Metric',
		'MetricSet'
	);

	private static $url_segment = 'metrics';

	private static $menu_title = 'Metrics';
}