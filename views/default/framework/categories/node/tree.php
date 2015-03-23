<?php

namespace hypeJunction\Categories;

use ElggEntity;
use ElggGroup;
use ElggSite;

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

if (Taxonomy::instanceOfCategory($entity)) {
	if ($entity->icontime) {
		$img = elgg_view('output/img', array(
			'src' => $entity->getIconURL('tiny')
		));
		$icon = elgg_format_element('span', array(
			'class' => 'categories-category-icon',
				), $img);
	}

	$container_guid = ELGG_ENTITIES_ANY_VALUE;
	$page_owner = elgg_get_page_owner_entity();
	if ($page_owner instanceof ElggGroup && HYPECATEGORIES_GROUP_CATEGORIES) {
		// only count items added to the group container
		$container_guid = $page_owner->guid;
	}
	$count = Taxonomy::getCategoryItems($entity->guid, array(
				'count' => true,
				'container_guids' => $container_guid,
	));
	$counter = elgg_format_element('span', array(
		'class' => 'cateogires-category-badge',
			), $count);

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