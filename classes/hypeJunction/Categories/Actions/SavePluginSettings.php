<?php

namespace hypeJunction\Categories\Actions;

use ElggPlugin;
use hypeJunction\Controllers\Action;

/**
 * @property array      $settings
 * @property ElggPlugin $plugin
 */
final class SavePluginSettings extends Action {

	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		parent::setup();
		$this->settings = $this->params->params;
		$this->plugin = elgg_get_plugin_from_id('hypeCategories');
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		if (!($this->plugin instanceof ElggPlugin)) {
			$this->result->addError(elgg_echo('plugins:settings:save:fail', array('hypeCategories')));
			return false;
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {

		$plugin_name = $this->plugin->getManifest()->getName();

		foreach ($this->settings as $k => $v) {
			if (is_array($v)) {
				$v = serialize($v);
			}
			var_dump($v);
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
