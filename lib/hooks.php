<?php

elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'hj_categories_category_icon_url');

function hj_categories_category_icon_url($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	$size = elgg_extract('size', $params, 'medium');

	if (!elgg_instanceof($entity, 'object', 'hjcategory')) {
		return $return;
	}

	return elgg_normalize_url("categories/icon/$entity->guid/$size");
}