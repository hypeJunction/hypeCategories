<?php

/* hypeCategories
 *
 * Content Categories
 * 
 * @package hypeJunction
 * @subpackage hypeCategories
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2011-2013, Ismayil Khayredinov
 */

define('HYPECATEGORIES_RELEASE', 1374851653);

elgg_register_event_handler('init', 'system', 'hj_categories_init');

function hj_categories_init() {

	elgg_register_classes(elgg_get_plugins_path() . 'hypeCategories/classes/');

	// Libraries
	$libraries = array(
		'base',
		'page_handlers',
		'actions',
		'assets',
		'views',
		'menus',
		'hooks'
	);

	foreach ($libraries as $lib) {
		$path = elgg_get_plugins_path() . "hypeCategories/lib/{$lib}.php";
		if (file_exists($path)) {
			elgg_register_library("categories:library:$lib", $path);
			elgg_load_library("categories:library:$lib");
		}
	}

	elgg_register_event_handler('upgrade', 'system', 'hj_categories_check_release');

}

/**
 * Run upgrade scripts
 *
 * @param string $event Equals 'upgrade'
 * @param string $type Equals 'system'
 * @param type $params
 * @return boolean
 */
function hj_gallery_check_release($event, $type, $params) {

	if (!elgg_is_admin_logged_in()) {
		return true;
	}

	$release = HYPECATEGORIES_RELEASE;
	$old_release = elgg_get_plugin_setting('release', 'hypeCategories');

	if ($release > $old_release) {

		elgg_register_library("categories:library:upgrade", elgg_get_plugins_path() . 'hypeCategories/lib/upgrade.php');
		elgg_load_library("categories:library:upgrade");

		elgg_set_plugin_setting('release', $release, 'hypeCategories');
	}

	return true;
}