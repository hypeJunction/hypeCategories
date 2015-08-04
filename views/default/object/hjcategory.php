<?php

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

	$items = hypeCategories()->categories->getItemsInCategory($entity, array('limit' => $limit));
	$body .= elgg_view_entity_list($items, array(
		'full_view' => false,
		'no_results' => elgg_echo('categories:empty'),
	));

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
	$container_guids = get_input('container_guid', ELGG_ENTITIES_ANY_VALUE);

	if ($types && $subtypes) {
		$options = array(
			'full_view' => false,
			'pagination' => true,
			'types' => $types,
			'subtypes' => $subtypes,
			'container_guids' => $container_guids,
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