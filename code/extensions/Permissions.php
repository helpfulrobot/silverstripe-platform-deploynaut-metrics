<?php
/**
 * DashboardMetrics\Permissions provides permission control for the environment metric viewing.
 */

namespace DashboardMetrics;

class Permissions extends \DNRoot implements \PermissionProvider {

	const ALLOW_ENVIRONMENT_METRICS_READ = 'ALLOW_ENVIRONMENT_METRICS_READ';

		/**
	 * Return a map of permission codes to add to the dropdown shown in the Security section of the CMS.
	 * array(
	 *   'VIEW_SITE' => 'View the site',
	 * );
	 */
	public function providePermissions() {
		return array(
			self::ALLOW_ENVIRONMENT_METRICS_READ => array(
				'name' => "Read access to environment metrics",
				'category' => "Deploynaut",
			)
		);
	}
}
