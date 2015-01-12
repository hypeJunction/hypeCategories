<?php

namespace hypeJunction\Categories;

$entity = elgg_extract('entity', $vars);

if (instanceof_category($entity)) {
	echo elgg_view_image_block('', elgg_view('forms/categories/edit', $vars), array(
		'class' => 'categories-category-block'
	));
} else {
	if (elgg_instanceof($entity, 'site')) {
		$attr = elgg_echo('categories:site');
	} else {
		$icon = elgg_view_entity_icon($entity, 'tiny');
		$attr = elgg_echo('categories:group', array($entity->name));
	}

	echo elgg_view_image_block($icon, $attr, array(
		'class' => 'categories-category-block'
	));
}
