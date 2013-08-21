<?php

elgg_register_event_handler('create', 'all', 'hj_categories_update_entity_categories');
elgg_register_event_handler('update', 'all', 'hj_categories_update_entity_categories');


/**
 * Update entity categories
 *
 * @param string $event Equals 'create' or 'update'
 * @param string $type Equals 'object', 'user' or 'group'
 * @param ElggEntity $entity
 * @return boolean
 */
function hj_categories_update_entity_categories($event, $type, $entity) {

	if (!elgg_instanceof($entity)) {
		return true;
	}

	$entity_guid = $entity->getGUID();

	$input_categories = get_input('categories', false);
	set_input('categories', false); // prevent this handler from running multiple times in case of nested actions

	// Category form input was not present
	if (!$input_categories) {
		return true;
	}

	// User did not specify any categories
	if ($input_categories && !is_array($input_categories)) {
		$input_categories = array();
	}

	$future_categories = array();

	foreach ($input_categories as $guid) {
		$category = get_entity($guid);
		$universal_categories[] = $category->title;
		$hierarchy = hj_categories_get_hierarchy($category->guid, true, true);
		$future_categories = array_merge($future_categories, $hierarchy);
	}

	// Storing categories metadata for compatibility with categories plugin
	$entity->universal_categories = $universal_categories;

	$current_categories = hj_categories_get_entity_categories($entity_guid, array(), true);

	$to_remove = array_diff($current_categories, $future_categories);
	$to_add = array_diff($future_categories, $current_categories);
	
	foreach ($to_remove as $guid) {
		remove_entity_relationship($entity_guid, HYPECATEGORIES_RELATIONSHIP, $guid);
	}

	foreach ($to_add as $guid) {
		add_entity_relationship($entity_guid, HYPECATEGORIES_RELATIONSHIP, $guid);
	}

	return true;
}