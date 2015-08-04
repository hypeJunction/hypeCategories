<?php

$entity = elgg_extract('entity', $vars);

if (!hypeCategories()->categories->instanceOfCategory($entity)) {
	return;
}

if ($entity->icontime) {
	$icon = elgg_view_entity_icon($entity, 'small');
}

$description = '';
if ($entity->description) {
	$description = elgg_view('output/longtext', array(
		'value' => $entity->description,
	));
}

$description = elgg_view_image_block($icon, $description);

$description .= elgg_view_menu('category-filter', array(
	'entity' => $entity,
	'class' => 'elgg-menu-page',
	'sort_by' => 'text',
		));

echo elgg_view_module('aside', $entity->getDisplayName(), $description);

