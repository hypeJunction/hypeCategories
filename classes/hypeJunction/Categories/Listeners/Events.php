<?php

namespace hypeJunction\Categories\Listeners;

use ElggEntity;
use hypeJunction\Categories\Config\Config;
use hypeJunction\Categories\Models\Model;
use hypeJunction\Categories\Services\Router;
use hypeJunction\Categories\Services\Upgrades;

/**
 * Events service
 */
class Events {

	/**
	 * Scripts to require on system upgrade
	 * @var array
	 */
	private $upgradeScripts = array(
		'activate.php',
	);
	
	private $config;
	private $router;
	private $model;
	private $upgrades;
	private $queue;

	/**
	 * Constructor
	 * @param Config   $config   Config
	 * @param Router   $router   Router
	 * @param Model $model Taxonomy
	 * @param Upgrades $upgrades Upgrades
	 */
	public function __construct(Config $config, Router $router, Model $model, Upgrades $upgrades) {
		$this->config = $config;
		$this->router = $router;
		$this->model = $model;
		$this->upgrades = $upgrades;
		$this->queue = array();
	}

	/**
	 * Run tasks on system init
	 * @return void
	 */
	public function init() {
		elgg_register_event_handler('pagesetup', 'system', array($this, 'pagesetup'));
		elgg_register_event_handler('upgarde', 'system', array($this, 'upgrade'));
		elgg_register_event_handler('create', 'all', array($this, 'updateEntityCategories'));
		elgg_register_event_handler('update', 'all', array($this, 'updateEntityCategories'));
	}

	/**
	 * Performs tasks on page setup
	 * @return void
	 */
	public function pagesetup() {

		elgg_register_menu_item('site', array(
			'name' => 'categories',
			'text' => elgg_echo('categories'),
			'href' => $this->router->normalize('all'),
		));

		if (elgg_is_admin_logged_in() && elgg_in_context('admin')) {
			elgg_register_menu_item('page', array(
				'name' => 'categories',
				'text' => elgg_echo('categories:site'),
				'href' => $this->router->normalize('manage'),
				'priority' => 500,
				'contexts' => array('admin'),
				'section' => 'configure'
			));
		}
	}

	/**
	 * Runs upgrade scripts
	 * @return bool
	 */
	protected function upgrade() {
		if (elgg_is_admin_logged_in()) {
			foreach ($this->upgradeScripts as $script) {
				$path = $this->plugin->getPath() . $script;
				if (file_exists($path)) {
					require_once $path;
				}
			}
			$this->upgrades->runUpgrades();
		}
		return true;
	}

	/**
	 * Checks request parameters for categories input values on ElggEntity::save()
	 * and updates entity categories
	 *
	 * @param string     $event  "create"|"update"
	 * @param string     $type   "all"
	 * @param ElggEntity $entity Entity
	 * @return bool
	 */
	public function updateEntityCategories($event, $type, $entity) {

		if (!$entity instanceof ElggEntity) {
			return true;
		}

		$entity_guid = $entity->getGUID();

		if (in_array($entity_guid, $this->queue)) {
			// No need to run this handler on multiple update events for this entity
			return true;
		}
		$this->queue[] = $entity_guid;

		if (!$this->model->isAllowed($entity)) {
			// Categories do not apply to this item
			return true;
		}

		$categories = get_input('categories', null);
		if (is_null($categories)) {
			// Category form input was not present
			return true;
		}

		$this->model->setItemCategories($entity, $categories);
		return true;
	}

}
