<?php

namespace hypeJunction\Categories;

$entity = elgg_extract('entity', $vars);
$full = elgg_extract('full_view', $vars, false);
$limit = get_input('limit', 5);
$size = elgg_extract('size', $vars, 'tiny');

if (!$full) {

	if ($entity->icontime) {
		$icon = elgg_view_entity_icon($entity, $size);
	}

	$title_link = elgg_view('output/url', array(
		'text' => $entity->getDisplayName(),
		'href' => $entity->getURL()
	));
	$title = elgg_view_image_block($icon, $title_link);

	$body = elgg_view('output/longtext', array(
		'value' => elgg_get_excerpt($entity->description)
	));

	$count = get_filed_items($entity->guid, array('count' => true));

	if ($count > 0) {

		$items = get_filed_items($entity->guid, array('limit' => $limit));
		$body .= elgg_view_entity_list($items, array(
			'full_view' => false
		));
	} else {
		$body .= elgg_autop(elgg_echo('categories:empty'));
	}

	if ($count > $limit) {
		$all = elgg_view('output/url', array(
			'text' => elgg_echo('categories:view_all'),
			'href' => $entity->getURL()
		));
	}

	echo elgg_view_module('aside', $title, $body, array(
		'footer' => $all
	));
} else {


	$types = get_input('type', elgg_get_config('taxonomy_types'));
	$subtypes = get_input('subtype', elgg_get_config('taxonomy_subtypes'));

	if ($types && $subtypes) {
		$options = array(
			'full_view' => false,
			'pagination' => true,
			'types' => $types,
			'subtypes' => $subtypes,
			'limit' => get_input('limit', 20),
			'relationship' => HYPECATEGORIES_RELATIONSHIP,
			'relationship_guid' => $entity->guid,
			'inverse_relationship' => true,
			'count' => true,
			'size' => $size,
		);

		$count = elgg_get_entities_from_relationship($options);
	}
	
	if ($count) {
		$options['count'] = false;
		$body .= elgg_list_entities_from_relationship($options);
	} else {
		$body .= elgg_autop(elgg_echo('categories:empty'));
	}

	echo $body;
}