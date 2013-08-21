<?php

/**
 * Get first level subcategories for a given container
 * 
 * @param int $container_guid Container GUID or an array of container GUIDs
 * @param array $params Additional parameters to be passed to the getter function
 * @return array Array of categories
 */
function hj_categories_get_subcategories($container_guid = null, $params = array()) {

	$defaults = array(
		'types' => 'object',
		'subtypes' => 'hjcategory',
		'order_by_metadata' => array('name' => 'priority', 'direction' => 'ASC', 'as' => 'integer'),
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
function hj_categories_get_filed_items($category_guid, $params = array()) {

	$defaults = array(
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
function hj_categories_get_entity_categories($entity_guid, $params = array(), $as_guids = false) {

	$defaults = array(
		'types' => 'object',
		'subtypes' => 'hjcategory',
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
function hj_categories_get_hierarchy($entity_guid, $as_guids = false, $self = false) {

	$entity = get_entity($entity_guid);
	
	while (elgg_instanceof($entity, 'object', 'hjcategory')) {
		$return[] = ($as_guids) ? $entity->guid : $entity;
		$entity = $entity->getContainerEntity();
	}

	if (!$self) {
		unset($return[0]);
	}
	
	return (sizeof($return)) ? array_reverse($return) : array();
}