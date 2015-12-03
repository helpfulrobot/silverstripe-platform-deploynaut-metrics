<?php
/**
 * DashboardMetrics\Permissions provides permission control for the environment metric viewing.
 */

namespace DashboardMetrics;

class Permissions extends \DNRoot implements \PermissionProvider {

	const ALLOW_ENVIRONMENT_CONFIG_READ = 'ALLOW_ENVIRONMENT_CONFIG_READ';

	// public static $allowed_actions = array(
	// 	'save'
	// );

	public function init() {
		parent::init();

		$project = $this->getCurrentProject();
		if(!$project) {
			return $this->project404Response();
		}

		if(!$project->allowed(self::ALLOW_ENVIRONMENT_CONFIG_READ)) {
			return \Security::permissionFailure();
		}
	}
}
