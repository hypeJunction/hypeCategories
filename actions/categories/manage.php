<?php

namespace hypeJunction\Categories;

use ElggFile;

$config = get_input('categories');
$hierarchy = json_decode(get_input('hierarchy'), true);
$root_guid = get_input('container_guid', elgg_get_site_entity()->guid);

$deleted = $success = $error = $empty = 0;

foreach ($config['hierarchy'] as $key => $node_id) {

	$guid = (int) $config['guid'][$key];
	$title = $config['title'][$key];
	$desc = $config['description'][$key];
	//$container_guid = $config['container_guid'][$key];
	$access_id = $config['access_id'][$key];
	$subtype = $config['subtype'][$key];
	$icon = '';

	$form_values = array();
	foreach ($config as $param_name => $entity_params) {
		$form_values[$param_name] = $entity_params[$key];
	}

	if ($guid && ($category = get_entity($guid))) {
		if (!$title) {
			$category->delete();
			$category = '';
			$deleted++;
		} else {
			$category->title = $title;
			$category->description = $desc;
			//$category->container_guid = $container_guid ?: $root_guid;
			$category->access_id = $access_id;
			$category->priority = $key;
			$category->setVolatileData('formValues', $form_values);
			if ($category->save()) {
				$success++;
			} else {
				$error++;
			}
		}
	} else if ($title) {
		$class = get_subtype_class('object', $subtype);
		if (!$class) {
			$class = '\\ElggObject';
		}
		$category = new $class();
		$category->subtype = ($subtype) ? $subtype : HYPECATEGORIES_SUBTYPE;
		$category->owner_guid = elgg_get_logged_in_user_guid();
		$category->container_guid = $root_guid;
		$category->title = $title;
		$category->description = $desc;
		$category->access_id = $access_id;
		$category->priority = $key;
		$category->setVolatileData('formValues', $form_values);
		if ($category->save()) {
			$success++;
		} else {
			$error++;
		}
	} else {
		$category = '';
		$empty++;
	}

	if (elgg_instanceof($category) && !empty($_FILES['categories']['name']['icon'][$key])) {
		$icontime = false;
		$icon_sizes = elgg_get_config('icon_sizes');

		$prefix = "icons/" . $category->getGUID();

		foreach ($icon_sizes as $size => $values) {

			$thumb_resized = get_resized_image_from_existing_file($_FILES['categories']['tmp_name']['icon'][$key], $values['w'], $values['h'], $values['square'], 0, 0, 0, 0, $values['upscale']);

			if ($thumb_resized) {

				$thumb = new ElggFile();
				$thumb->owner_guid = $category->owner_guid;
				$thumb->setMimeType('image/jpeg');
				$thumb->setFilename($prefix . "$size.jpg");
				$thumb->open("write");
				$thumb->write($thumb_resized);
				$thumb->close();

				$icontime = true;
			}
		}

		if ($icontime) {
			$category->icontime = time();
		}
	}

	$nodes[$node_id] = $category;
}

foreach ($hierarchy as $key => $root) {
	update_hierarchy($root['id'], $root['children'], $nodes);
}

function update_hierarchy($node_id, $children, $nodes) {

	$category = $nodes[$node_id];

	if ($children) {
		foreach ($children as $child) {
			$child_node_id = $child['id'];
			$child_children = $child['children'];
			$subcategory = $nodes[$child_node_id];
			elgg_log("Updating taxonomy; parent node id $node_id -> child node id $child_node_id", 'WARNING');

			if ($category->guid == $subcategory->guid) {
				continue;
			}

			if (elgg_instanceof($category) && elgg_instanceof($subcategory)) {
				$subcategory->container_guid = $category->guid;
				$subcategory->save();
			} else if (!elgg_instanceof($category) && elgg_instanceof($subcategory)) {
//				$subcategory->delete();
			}
			update_hierarchy($child_node_id, $child_children, $nodes);
		}
	}
}

forward(REFERER);
