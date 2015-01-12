<?php

namespace hypeJunction\Categories;

use ElggEntity;
use ElggMenuItem;

/**
 * Get first level subcategories for a given container
 * 
 * @param int $container_guid Container GUID or an array of container GUIDs
 * @param array $params Additional parameters to be passed to the getter function
 * @return array Array of categories
 */
function get_subcategories($container_guid = null, $params = array()) {

	$dbprefix = elgg_get_config('dbprefix');
	$nid = elgg_get_metastring_id('priority');
	$defaults = array(
		'types' => 'object',
		'subtypes' => get_category_subtypes(),
		'joins' => array(
			"LEFT JOIN {$dbprefix}metadata md ON md.name_id = $nid",
			"LEFT JOIN {$dbprefix}metastrings msv ON msv.id = md.value_id",
		),
		'order_by' => 'ISNULL(msv.string), CAST(msv.string as SIGNED) ASC',
		'limit' => 9999
	);

	$params = array_merge($defaults, $params);

	if (!$container_guid) {
		$site = elgg_get_site_entity();
		$container_guid = $site->guid;
	}

	$params['container_guids'] = $container_guid;

	return elgg_get_entities_from_metadata($params);
}

/**
 * Get entities filed under this category
 *
 * @param int $category_guid GUID of the category
 * @param array $params Additional parameters to be passed to the getter function
 * @return array Array of filed items
 */
function get_filed_items($category_guid, $params = array()) {

	$defaults = array(
		'types' => elgg_get_config('taxonomy_types'),
		'subtypes' => elgg_get_config('taxonomy_subtypes'),
		'relationship' => HYPECATEGORIES_RELATIONSHIP,
		'inverse_relationship' => true
	);

	$params = array_merge($defaults, $params);

	$params['relationship_guid'] = $category_guid;

	return elgg_get_entities_from_relationship($params);
}

/**
 * Get categories an entity is filed in
 *
 * @param int $entity_guid GUID of an entity
 * @param array $params Additional parameters to be passed to the getter function
 * @param bool $as_guids Return an array of GUIDs
 * @return array Array of filed items
 */
function get_entity_categories($entity_guid, $params = array(), $as_guids = false) {

	$defaults = array(
		'types' => 'object',
		'subtypes' => get_category_subtypes(),
		'reltionship' => HYPECATEGORIES_RELATIONSHIP,
		'inverse_relationship' => false,
		'limit' => false
	);

	$params = array_merge($defaults, $params);

	$params['relationship_guid'] = $entity_guid;

	$categories = elgg_get_entities_from_relationship($params);

	if ($as_guids && $categories) {
		foreach ($categories as $key => $category) {
			$categories[$key] = $category->guid;
		}
	}

	return $categories;
}

/**
 * Build an array of categories from top level parent category to the current category
 * 
 * @param int $entity_guid GUID of the current category
 * @param bool $as_guids Return an array of guids instead of objects
 * @param bool $self Include current category
 * @return array An array of categories or category guids
 */
function get_hierarchy($entity_guid, $as_guids = false, $self = false) {

	$entity = get_entity($entity_guid);

	while (instanceof_category($entity)) {
		$return[] = ($as_guids) ? $entity->guid : $entity;
		$entity = $entity->getContainerEntity();
	}

	if (!$self) {
		unset($return[0]);
	}

	return (sizeof($return)) ? array_reverse($return) : array();
}

/**
 * Add a category menu item with its underlying taxonomy
 *
 * @param ElggEntity $entity Site, group or category
 * @param array $params An array of additional parameters
 * @return array An array of menu items
 */
function add_tree_node($entity, $params = array()) {

	$container = $entity->getContainerEntity();

	$params['entity'] = $entity;
	if (!isset($params['level'])) {
		$params['level'] = 0;
	}

	if (instanceof_category($container) && $params['level'] > 0) {
		$root = array(
			'name' => "category:$entity->guid",
			'text' => elgg_view('framework/categories/node', $params),
			'href' => false,
			'priority' => $entity->priority,
			'parent_name' => ($params['level'] > 0) ? "category:$container->guid" : null,
			'data-guid' => $entity->guid
		);
	} else if (instanceof_category($entity)) {
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

	$params['level'] ++;

	$root_menu_item = ElggMenuItem::factory($root);
	$return[] = $root_menu_item;

	if (HYPECATEGORIES_GROUP_TREE_SITE && elgg_instanceof($entity, 'group') && !elgg_in_context('categories-manage')) {
		$categories = get_subcategories(array(elgg_get_site_entity()->guid, $entity->guid));
	} else {
		$categories = get_subcategories($entity->guid);
	}

	if (is_array($categories)) {
		foreach ($categories as $category) {
			$submenu = add_tree_node($category, $params);
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

/**
 * Returns entity subtypes that represent categories
 * @return array
 */
function get_category_subtypes() {

	$subtypes = elgg_get_config('taxonomy_tree_subtypes');
	if (!is_array($subtypes)) {
		$subtypes = array(HYPECATEGORIES_SUBTYPE);
	}

	return $subtypes;
}

/**
 * Checks if entity is a category
 *
 * @param ElggEntity $entity   Entity to check
 * @param array      $subtypes Override subtypes to check against
 * @return bool
 */
function instanceof_category($entity, $subtypes = null) {
	if (!is_array($subtypes)) {
		$subtypes = get_category_subtypes();
	}
	return elgg_instanceof($entity, 'object') && in_array($entity->getSubtype(), $subtypes);
}
