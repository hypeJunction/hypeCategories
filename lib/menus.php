<?php

elgg_register_menu_item('site', array(
	'name' => 'categories',
	'text' => elgg_echo('hj:categories'),
	'href' => 'categories/all'
));

if (elgg_is_admin_logged_in()) {

	elgg_register_menu_item('page', array(
		'name' => 'categories',
		'text' => elgg_echo('hj:categories:site'),
		'href' => 'categories/manage',
		'priority' => 500,
		'contexts' => array('admin'),
		'section' => 'configure'
	));
	
}

elgg_register_plugin_hook_handler('register', 'menu:entity', 'hj_categories_entity_menu_setup');
elgg_register_plugin_hook_handler('register', 'menu:categories', 'hj_categories_tree_menu_setup');


/**
 * Add categories to the entity menu
 *
 * @param str $hook Equals 'register'
 * @param str $type Equals 'menu:entity'
 * @param array $return An array of menu items
 * @param array $params An array of additional parameters
 * @return array An array of menu items
 */
function hj_categories_entity_menu_setup($hook, $type, $return, $params) {

	if (!HYPECATEGORIES_ENTITY_MENU) {
		return $return;
	}

	$entity = elgg_extract('entity', $params);

	if (!elgg_instanceof($entity)) {
		return $return;
	}

	$categories = hj_categories_get_entity_categories($entity->guid, array('count' => true));

	if ($categories) {
		$return[] = ElggMenuItem::factory(array(
			'name' => 'categories',
			'text' => elgg_view('output/category', array(
				'entity' => $entity
			)),
			'href' => false,
		));
	}

	return $return;
}

/**
 * Setup categories tree using Elgg menu
 *
 * @param string $hook Equals 'register'
 * @param string $type Equals 'menu:categories'
 * @param array $return An array of category menu items
 * @param array $params Additional parameters passed to elgg_view_menu()
 * @return array An array of category menu items
 */
function hj_categories_tree_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, elgg_get_site_entity()); // container group or site or category

	if (!elgg_instanceof($entity, 'site') 
			&& !elgg_instanceof($entity, 'group')
			&& !elgg_instanceof($entity, 'object', 'hjcategory')) {
		return $return;
	}

	$params['level'] = 0;
	$return = hj_categories_add_tree_node($entity, $params);

	return $return;
}

/**
 * Add a category menu item with its underlying taxonomy
 *
 * @param ElggEntity $entity Site, group or category
 * @param array $params An array of additional parameters
 * @return array An array of menu items
 */
function hj_categories_add_tree_node($entity, $params = array()) {

	$container = $entity->getContainerEntity();

	$params['entity'] = $entity;
	if (!isset($params['level'])) {
		$params['level'] = 0;
	}
	
	if (elgg_instanceof($container, 'object', 'hjcategory')
			&& $params['level'] > 0) {
		$root = array(
			'name' => "category:$entity->guid",
			'text' => elgg_view('framework/categories/node', $params),
			'href' => false,
			'priority' => $entity->priority,
			'parent_name' => ($params['level'] > 0) ? "category:$container->guid" : null,
			'data-guid' => $entity->guid
		);
	} else if (elgg_instanceof($entity, 'object', 'hjcategory')) {
		$root = array(
			'name' => "category:$entity->guid",
			'text' => elgg_view('framework/categories/node', $params),
			'href' => false,
			'priority' => $entity->priority,
			'parent_name' => ($params['level'] > 0) ? "root" : null,
			'data-guid' => $entity->guid
		);
	} else {
		$root = array(
			'name' => "root",
			'text' => elgg_view('framework/categories/node', $params),
			'href' => false,
			'priority' => $entity->priority,
			'data-guid' => $entity->guid
		);
	}

	$params['level']++;
	
	$root_menu_item = ElggMenuItem::factory($root);
	$return[] = $root_menu_item;

	if (HYPECATEGORIES_GROUP_TREE_SITE
			&& elgg_instanceof($entity, 'group')
			&& !elgg_in_context('categories-manage')) {
		$categories = hj_categories_get_subcategories(array(elgg_get_site_entity()->guid, $entity->guid));
	} else {
		$categories = hj_categories_get_subcategories($entity->guid);
	}

	if (is_array($categories)) {
		foreach ($categories as $category) {
			$submenu = hj_categories_add_tree_node($category, $params);
			foreach ($submenu as $submenu_item) {
				$return[] = $submenu_item;
			}
		}
	}

	if ($entity->canEdit() && elgg_in_context('categories-manage')) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'placeholder',
					'text' => elgg_view('framework/categories/placeholder', array(
						'container' => $entity
					)),
					'href' => false,
					'priority' => 999,
					'parent_name' => $root_menu_item->getName(),
					'data-guid' => $entity->guid
		));
	}

	return $return;
}