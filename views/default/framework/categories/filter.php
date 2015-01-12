<?php

namespace hypeJunction\Categories;

$entity = elgg_extract('entity', $vars);

if (!instanceof_category($entity)) {
	return true;
}

if ($entity->icontime) {
	$icon = elgg_view_entity_icon($entity, 'small');
}

$description = ($entity->description) ? elgg_view('output/longtext', array(
	'value' => $entity->description,
		)) : '';

$taxonomy_type_subtype_pairs = elgg_get_config('taxonomy_type_subtype_pairs');
foreach ($taxonomy_type_subtype_pairs as $tsp) {

	list($type, $subtype) = explode(':', $tsp);

	$count = elgg_get_entities_from_relationship(array(
		'types' => $type,
		'subtypes' => ($subtype == 'default') ? null : $subtype,
		'relationship' => HYPECATEGORIES_RELATIONSHIP,
		'relationship_guid' => $entity->guid,
		'inverse_relationship' => true,
		'count' => true
	));

	if ($count) {
		elgg_register_menu_item('category-filter', array(
			'name' => "$type:$subtype",
			'text' => (($subtype == 'default') ? elgg_echo("item:$type") : elgg_echo("item:$type:$subtype")) . '<em>' . $count . '</em>',
			'href' => elgg_http_add_url_query_elements($entity->getURL(), array('type' => $type, 'subtype' => $subtype)),
			'selected' => ($type == get_input('type') && $subtype == get_input('subtype'))
		));
	}
}

$description = elgg_view_image_block($icon, $description);

$description .= elgg_view_menu('category-filter', array(
	'class' => 'elgg-menu-page'
		));

echo elgg_view_module('aside', $entity->getDisplayName(), $description);

