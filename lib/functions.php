<?php

namespace hypeJunction\Categories;

use ElggBatch;
use ElggEntity;
use ElggMenuItem;
use ElggObject;

/**
 * Get first level subcategories for a given container
 * 
 * @param int   $container_guid Container GUID or an array of container GUIDs
 * @param array $params         Additional parameters to be passed to the getter function
 * @return ElggObject[]|false Array of categories
 * @deprecated since version 3.1
 */
function get_subcategories($container_guid = null, $params = array()) {
	if (is_null($container_guid)) {
		$container_guid = elgg_get_site_entity()->guid;
	}

	$batch = hypeCategories()->categories->getSubcategories($container_guid, $params);

	if ($batch instanceof ElggBatch || is_array($batch)) {
		$categories = array();
		foreach ($batch as $b) {
			$categories[] = $b;
		}
		return $categories;
	}

	return $batch;
}

/**
 * Get entities filed under this category
 *
 * @param int   $category_guid GUID of the category
 * @param array $params        Additional parameters to be passed to the getter function
 * @return ElggEntity[]|false Array of filed items
 * @deprecated since version 3.1
 */
function get_filed_items($category_guid, $params = array()) {
	
	$category = get_entity($category_guid);
	if (!$category) {
		return false;
	}
	
	$items = array();
	$batch = hypeCategories()->categories->getItemsInCategory($category, $params);
	if ($batch instanceof ElggBatch || is_array($batch)) {
		foreach ($batch as $b) {
			$items[] = $b;
		}
		return $items;
	}
	return $batch;
}

/**
 * Get categories an entity is filed in
 *
 * @param int   $entity_guid GUID of an entity
 * @param array $params      Additional parameters to be passed to the getter function
 * @param bool  $as_guids    Return an array of GUIDs
 * @return ElggEntity[]|int[]|false Array of filed items
 * @deprecated since version 3.1
 */
function get_entity_categories($entity_guid, $params = array(), $as_guids = false) {

	$entity = get_entity($entity_guid);
	if (!$entity) {
		return false;
	}
	$batch = hypeCategories()->categories->getItemCategories($entity, $params, $as_guids);
	if ($batch instanceof ElggBatch || is_array($batch)) {
		$categories = array();
		foreach ($batch as $b) {
			$categories[] = $b;
		}
		return $categories;
	}
	return $batch;
}

/**
 * Build an array of categories from top level parent category to the current category
 * 
 * @param int  $entity_guid GUID of the current category
 * @param bool $as_guids    Return an array of guids instead of objects
 * @param bool $self        Include current category
 * @return array An array of categories or category guids
 * @deprecated since version 3.1
 */
function get_hierarchy($entity_guid, $as_guids = false, $self = false) {
	$entity = get_entity($entity_guid);
	if (!$entity) {
		return array();
	}
	return hypeCategories()->categories->getHierarchy($entity, $as_guids, $self);
}

/**
 * Add a category menu item with its underlying taxonomy
 *
 * @param ElggEntity $entity Site, group or category
 * @param array      $params An array of additional parameters
 * @return ElggMenuItem[] An array of menu items
 * @deprecated since version 3.1
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

	if (!empty($categories)) {
		foreach ($categories as $category) {
			$submenu = add_tree_node($category, $params);
			foreach ($submenu as $submenu_item) {
				$return[] = $submenu_item;
			}
		}
	}



	return $return;
}

/**
 * Returns entity subtypes that represent categories
 * @return array
 * @deprecated since version 3.1
 */
function get_category_subtypes() {
	return hypeCategories()->config->getCategorySubtypes();
}

/**
 * Checks if entity is a category
 *
 * @param ElggEntity $entity   Entity to check
 * @param array      $subtypes Override subtypes to check against
 * @return bool
 * @deprecated since version 3.1
 */
function instanceof_category($entity, $subtypes = null) {
	if (!is_array($subtypes)) {
		$subtypes = get_category_subtypes();
	}
	return elgg_instanceof($entity, 'object') && in_array($entity->getSubtype(), $subtypes);
}
