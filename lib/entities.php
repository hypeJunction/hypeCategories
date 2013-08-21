<?php

elgg_register_entity_type('object', 'hjcategory');

elgg_register_entity_url_handler('object', 'hjcategory', 'hj_categories_category_url_handler');

/**
 * Category URL
 * @param ElggObject $entity Category object
 * @return string URL
 */
function hj_categories_category_url_handler($entity) {
	$friendly = elgg_get_friendly_title($entity->title);

	$page_owner = elgg_get_page_owner_entity();

	if (HYPECATEGORIES_GROUP_CATEGORIES && elgg_instanceof($page_owner, 'group')) {
		return elgg_normalize_url("categories/group/$page_owner->guid/$entity->guid/$friendly");
	} else {
		return elgg_normalize_url("categories/view/$entity->guid/$friendly");
	}
}