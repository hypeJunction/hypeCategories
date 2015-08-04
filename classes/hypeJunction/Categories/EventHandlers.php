<?php

namespace hypeJunction\Categories;

use ElggEntity;
use hypeJunction\Categories\Config;
use hypeJunction\Categories\Categories;
use hypeJunction\Categories\Router;

/**
 * Events service
 */
class EventHandlers {
	
	private $config;
	private $router;
	private $categories;
	private $queue;

	/**
	 * Constructor
	 * 
	 * @param Config     $config     Config
	 * @param Router     $router     Router
	 * @param Categories $categories Categories lib
	 */
	public function __construct(Config $config, Router $router, Categories $categories) {
		$this->config = $config;
		$this->router = $router;
		$this->categories = $categories;
		$this->queue = array();
	}

	/**
	 * Performs tasks on page setup
	 * @return void
	 */
	public function pagesetup() {

		$item1 = elgg_register_menu_item('site', array(
			'name' => 'categories',
			'text' => elgg_echo('categories'),
			'href' => $this->router->normalize('all'),
		));

		if (elgg_is_admin_logged_in() && elgg_in_context('admin')) {
			$item2 = elgg_register_menu_item('page', array(
				'name' => 'categories',
				'text' => elgg_echo('categories:site'),
				'href' => $this->router->normalize('manage'),
				'priority' => 500,
				'contexts' => array('admin'),
				'section' => 'configure'
			));
		}

		return $item1 && $item2;
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

		if (!$this->categories->isAllowed($entity)) {
			// Categories do not apply to this item
			return true;
		}

		$categories = get_input('categories', null);
		if (is_null($categories)) {
			// Category form input was not present
			return true;
		}

		$this->categories->setItemCategories($entity, $categories);
		return true;
	}

}
