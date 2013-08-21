<?php

$entity = elgg_extract('entity', $vars);
$full = elgg_extract('full_view', $vars, false);
$limit = get_input('limit', 5);

if (!$full) {

	if ($entity->icontime) {
		$icon = elgg_view_entity_icon($entity, 'small');
	}

	$title = elgg_view_image_block($icon, $entity->title);

	$body = elgg_view('output/longtext', array(
		'value' => $entity->description
	));

	$count = hj_categories_get_filed_items($entity->guid, array('count' => true));

	if ($count > 0) {

		$items = hj_categories_get_filed_items($entity->guid, array('limit' => $limit));
		$body .= elgg_view_entity_list($items, array(
			'full_view' => false
		));
	} else {
		$body .= elgg_autop(elgg_echo('hj:categories:empty'));
	}

	if ($count > $limit) {
		$all = elgg_view('output/url', array(
			'text' => elgg_echo('hj:categories:view_all'),
			'href' => $entity->getURL()
		));
	}

	echo elgg_view_module('featured', $title, $body, array(
		'footer' => $all
	));
} else {

	if ($entity->icontime) {
		$icon = elgg_view_entity_icon($entity, 'medium');
	}

	$description = elgg_view('output/longtext', array(
		'value' => $entity->description,
		'class' => 'elgg-text-help mbl'
	));

	$registered_entities = elgg_get_config('registered_entities');
	foreach ($registered_entities as $type => $subtypes) {

		if (!sizeof($subtypes)) {
			$subtypes = array('default');
		}

		foreach ($subtypes as $subtype) {

			if ($subtype == 'hjcategory')
				continue;

			$count = elgg_get_entities_from_relationship(array(
				'types' => $type,
				'subtypes' => ($subtype == 'default') ? null : $subtype,
				'relationship' => HYPECATEGORIES_RELATIONSHIP,
				'relationship_guid' => $entity->guid,
				'inverse_relationship' => true,
				'count' => true
			));

			elgg_register_menu_item('category-filter', array(
				'name' => "$type:$subtype",
				'text' => elgg_echo('hj:categories:filter:type', array(($subtype == 'default') ? elgg_echo("item:$type") : elgg_echo("item:$type:$subtype"), $count)),
				'href' => elgg_http_add_url_query_elements($entity->getURL(), array('type' => $type, 'subtype' => $subtype)),
				'selected' => ($type == get_input('type') && $subtype == get_input('subtype'))
			));
		}
	}

	$sidebar = $icon;
	$sidebar .= $description;
	$sidebar .= elgg_view_menu('category-filter', array(
		'class' => 'elgg-menu-page'
	));

	$options = array(
		'full_view' => false,
		'pagination' => true,
		'types' => get_input('type', null),
		'subtypes' => get_input('subtype', null),
		'limit' => get_input('limit', 20),
		'relationship' => HYPECATEGORIES_RELATIONSHIP,
		'relationship_guid' => $entity->guid,
		'inverse_relationship' => true,
		'count' => true
	);

	$count = elgg_get_entities_from_relationship($options);
	if ($count) {
		$options['count'] = false;
		$body .= elgg_list_entities_from_relationship($options);
	} else {
		$body .= elgg_autop(elgg_echo('hj:categories:empty'));
	}

	echo elgg_view_image_block($sidebar, $body, array(
		'class' => 'categories-category-full'
	));
}