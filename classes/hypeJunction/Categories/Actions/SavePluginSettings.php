<?php

namespace hypeJunction\Categories\Actions;

use ElggPlugin;
use hypeJunction\Categories\Controllers\Actions\Action;

final class SavePluginSettings extends Action {

	private $params;
	private $plugin;

	public function validate() {
		$this->params = get_input('params', array(), false);
		$this->plugin = elgg_get_plugin_from_id('hypeCategories');

		if (!($this->plugin instanceof ElggPlugin)) {
			$this->result->addError(elgg_echo('plugins:settings:save:fail', array('hypeCategories')));
			return false;
		}

		return true;
	}
	
	public function execute() {

		$plugin_name = $this->plugin->getManifest()->getName();

		foreach ($this->params as $k => $v) {
			if (is_array($v)) {
				$v = serialize($v);
			}
			$result = $this->plugin->setSetting($k, $v);
			if (!$result) {
				$this->result->addError(elgg_echo('plugins:settings:save:fail', array($plugin_name)));
			}
		}

		if ($result) {
			$this->result->addMessage(elgg_echo('plugins:settings:save:ok', array($plugin_name)));
		}
	}

}
