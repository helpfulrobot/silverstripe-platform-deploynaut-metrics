<?php
/**
 * DashboardMetrics\Permissions provides permission control for the environment metric viewing.
 */

namespace DashboardMetrics;

class Permissions implements \PermissionProvider {

	const ALLOW_ENVIRONMENT_METRICS_READ = 'ALLOW_ENVIRONMENT_METRICS_READ';

	public function providePermissions() {
		return array(
			self::ALLOW_ENVIRONMENT_METRICS_READ => array(
				'name' => "Read access to environment metrics",
				'category' => "Deploynaut",
			)
		);
	}
}
