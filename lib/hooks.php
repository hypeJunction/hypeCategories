<?php

namespace hypeJunction\Categories;

use ElggMenuItem;

/**
 * Update category icon URL
 *
 * @param string $hook		Equals 'entity:icon:url'
 * @param string $type		Equals 'object'
 * @param string $return	Current icon URL
 * @param array $params		Additional params
 * @return string			Updated icon URL
 */
function category_icon_url($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	$size = elgg_extract('size', $params, 'medium');

	if (!elgg_instanceof($entity, 'object', HYPECATEGORIES_SUBTYPE)) {
		return $return;
	}

	return elgg_normalize_url(PAGEHANDLER . '/icon/' . $entity->guid . '/' . $size);
}

/**
 * Add categories to the entity menu
 *
 * @param str $hook Equals 'register'
 * @param str $type Equals 'menu:entity'
 * @param array $return An array of menu items
 * @param array $params An array of additional parameters
 * @return array An array of menu items
 */
function entity_menu_setup($hook, $type, $return, $params) {

	if (!HYPECATEGORIES_ENTITY_MENU) {
		return $return;
	}

	$entity = elgg_extract('entity', $params);

	if (!elgg_instanceof($entity)) {
		return $return;
	}

	$categories = get_entity_categories($entity->guid, array('count' => true));

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
function tree_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, elgg_get_site_entity()); // container group or site or category

	if (!elgg_instanceof($entity, 'site')
			&& !elgg_instanceof($entity, 'group')
			&& !elgg_instanceof($entity, 'object', HYPECATEGORIES_SUBTYPE)) {
		return $return;
	}

	$params['level'] = 0;
	$return = add_tree_node($entity, $params);

	return $return;
}
