<?php

namespace hypeJunction\Categories;

use ElggEntity;

/**
 * Add some menu items during page setup
 */
function pagesetup() {

	elgg_register_menu_item('site', array(
		'name' => 'categories',
		'text' => elgg_echo('categories'),
		'href' => PAGEHANDLER . '/all'
	));

	if (elgg_is_admin_logged_in()) {

		elgg_register_menu_item('page', array(
			'name' => 'categories',
			'text' => elgg_echo('categories:site'),
			'href' => PAGEHANDLER . '/manage',
			'priority' => 500,
			'contexts' => array('admin'),
			'section' => 'configure'
		));
		
	}
}

/**
 * Run upgrade scripts
 */
function upgrade() {

	if (!elgg_is_admin_logged_in()) {
		return true;
	}

	$release = HYPECATEGORIES_RELEASE;
	$old_release = elgg_get_plugin_setting('release', PLUGIN_ID);

	if ($release > $old_release) {

		include_once dirname(dirname(__FILE__)) . '/lib/upgrade.php';
		elgg_set_plugin_setting('release', $release, PLUGIN_ID);
	}

	return true;
}

/**
 * Update entity categories
 *
 * @param string $event		Equals 'create' or 'update'
 * @param string $type		Equals 'object', 'user' or 'group'
 * @param ElggEntity $entity
 * @return boolean
 */
function update_entity_categories($event, $type, $entity) {

	if (!elgg_instanceof($entity)) {
		return true;
	}

	$entity_guid = $entity->getGUID();

	// No need to run this handler on multiple update events for this entity
	global $TAXONOMY_CATCH;
	if (isset($TAXONOMY_CATCH[$entity_guid])) {
		return true;
	}
	$TAXONOMY_CATCH[$entity_guid] = true;

	// Restrict the scope of the handler to entity types/subtypes specified in the plugin settings
	$type = $entity->getType();
	$subtype = $entity->getSubtype();
	if (!$subtype) {
		$subtype = 'default';
	}

	$taxonomy_type_subtype_pairs = elgg_get_config('taxonomy_type_subtype_pairs');
	if (!is_array($taxonomy_type_subtype_pairs) || !in_array("$type:$subtype", $taxonomy_type_subtype_pairs)) {
		return true;
	}

	$input_categories = get_input('categories', false);

	//set_input('categories', false); // prevent this handler from running multiple times in case of nested actions
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
		$hierarchy = get_hierarchy($category->guid, true, true);
		$future_categories = array_merge($future_categories, $hierarchy);
	}

	// Storing categories metadata for compatibility with categories plugin
	$entity->universal_categories = $universal_categories;

	$current_categories = get_entity_categories($entity_guid, array(), true);

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
