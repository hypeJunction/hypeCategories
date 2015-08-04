<?php

$entity = elgg_extract('entity', $vars);

if (hypeCategories()->categories->instanceOfCategory($entity)) {
	echo elgg_view_image_block('', elgg_view('forms/categories/edit', $vars), array(
		'class' => 'categories-category-block'
	));
} else {
	if ($entity instanceof ElggSite) {
		$icon = '';
		$attr = elgg_echo('categories:site');
	} else if ($entity instanceof ElggGroup) {
		$icon = elgg_view_entity_icon($entity, 'tiny');
		$attr = elgg_echo('categories:group', array($entity->getDisplayName()));
	} else {
		$icon = elgg_view_entity_icon($entity, 'tiny');
		$attr = $entity->getDisplayName();
	}

	echo elgg_view_image_block($icon, $attr, array(
		'class' => 'categories-category-block'
	));
}
