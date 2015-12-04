<?php

namespace DashboardMetrics;

class MenuExtension extends \DataExtension {

	/**
	 * Add the "metrics" menu item to the environment screen.
	 *
	 * @param \ArrayList $list
	 */
	public function updateMenu(\ArrayList $list) {
		// if (!$this->owner->Backend()->config()->supports_environment_config) return;
		if(!$this->owner->Project()->allowed(Permissions::ALLOW_ENVIRONMENT_METRICS_READ)) return;

		$controller = \Controller::curr();
		$actionType = $controller->getField('CurrentActionType');

		$list->push(new \ArrayData(array(
			'Link' => sprintf(
				'naut/project/%s/environment/%s/metrics',
				$this->owner->Project()->Name,
				$this->owner->Name
			),
			'Title' => 'Metrics',
			'IsCurrent' => $this->owner->isCurrent(),
			'IsSection' => $this->owner->isSection() && $actionType == Dispatcher::ACTION_METRICS
		)));
	}

}