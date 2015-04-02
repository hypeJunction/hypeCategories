<?php

namespace hypeJunction\Categories\Services;

use hypeJunction\Categories\Config\Config;
use hypeJunction\Categories\Models\Model;

class Upgrades {

	private $config;
	private $model;
	
	/**
	 * Constructor
	 * @param Config   $config   Config
	 * @param Model $model Taxonomy
	 */
	public function __construct(Config $config, Model $model) {
		$this->config = $config;
		$this->model = $model;
	}

	/**
	 * Returns an array of upgrade scripts
	 * @return array
	 */
	private function getUpgrades() {
		return array(
		);
	}

	/**
	 * Runs pending upgrades
	 * @return void
	 */
	public function runUpgrades() {
		$site = elgg_get_site_entity();
		$upgrades = $this->getUpgrades();
		foreach ($upgrades as $upgrade) {
			$upgradename = "hypeCategories_$upgrade";
			if (get_private_setting($site->guid, $upgradename)) {
				continue;
			}
			if (is_callable(array($this, $upgrade))) {
				call_user_func(array($this, $upgrade));
				set_private_setting($site->guid, $upgradename, time());
			}
		}
	}
}
