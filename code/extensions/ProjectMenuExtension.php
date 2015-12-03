<?php

class ProjectMenuExtension extends DataExtension {

	/**
	 * [updateMenu description]
	 * @param  [type] $list [description]
	 * @return [type]       [description]
	 * @todo fix ACTION_SNAPSHOT
	 */
	public function updateMenu($list) {

		$controller = Controller::curr();
		$actionType = $controller->getField('CurrentActionType');

		$list->push(new ArrayData(array(
			'Link' => sprintf('naut/project/%s/metrics', $this->owner->Name),
			'Title' => 'Metrics',
			'IsCurrent' => $this->owner->isSection() && $controller->getAction() == 'metrics',
			'IsSection' => $this->owner->isSection() && $actionType == DNRoot::ACTION_SNAPSHOT
		)));

	}

}