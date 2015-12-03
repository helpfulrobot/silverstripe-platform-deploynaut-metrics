<?php
/**
 * DashboardMetrics\Permissions provides permission control for the environment metric viewing.
 */

namespace DashboardMetrics;

class Permissions extends \DNRoot implements \PermissionProvider {

	const ALLOW_ENVIRONMENT_METRICS_READ = 'ALLOW_ENVIRONMENT_METRICS_READ';

	public function init() {
		parent::init();

		// Performs canView permission check by limiting visible projects
		$project = $this->getCurrentProject();
		if(!$project) {
			return $this->project404Response();
		}

		if(!$project->allowed(self::ALLOW_ENVIRONMENT_METRICS_READ)) {
			return \Security::permissionFailure();
		}
	}
	public function providePermissions() {
		return array(
			self::ALLOW_ENVIRONMENT_METRICS_READ => array(
				'name' => "Read access to environment metrics",
				'category' => "Deploynaut",
			)
		);
	}
}
