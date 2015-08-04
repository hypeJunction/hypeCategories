<?php

$entity = elgg_extract('entity', $vars);
$badge = elgg_extract('badge', $vars, true);
$icons = elgg_extract('icons', $vars, true);

if (!$entity instanceof ElggEntity) {
	return;
}

if (hypeCategories()->categories->instanceOfCategory($entity)) {

	$icon = '';
	if ($entity->icontime) {
		$icon = elgg_view_entity_icon($entity, 'tiny', array(
			'href' => false,
		));
	}
	$counter = '';
	if ($badge) {
		$container_guid = ELGG_ENTITIES_ANY_VALUE;
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof ElggGroup) {
			// only count items added to the group container
			$container_guid = $page_owner->guid;
		}
		$count = hypeCategories()->categories->getItemsInCategory($entity, array(
			'count' => true,
			'container_guids' => $container_guid,
		));
		$counter = elgg_format_element('span', array(
			'class' => 'categories-category-badge',
				), $count);
	}

	$title = elgg_echo('categories:category:title', array($entity->getDisplayName(), $counter));

	echo elgg_view('output/url', array(
		'text' => $icon . $title,
		'href' => $entity->getURL(),
		'class' => 'categories-category-label',
	));
} else if ($entity instanceof ElggSite) {
	echo elgg_format_element('span', array(
		'class' => 'categories-category-label',
			), elgg_echo('categories:site'));
} else if ($entity instanceof ElggGroup) {
	echo elgg_format_element('span', array(
		'class' => 'categories-category-label',
			), elgg_echo('categories:group', array($entity->getDisplayName())));
} else {
	echo elgg_format_element('span', array(
		'class' => 'categories-category-label',
			), $entity->getDisplayName());
}