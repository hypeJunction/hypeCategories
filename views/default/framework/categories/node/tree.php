<?php

namespace hypeJunction\Categories;

$entity = elgg_extract('entity', $vars);
$page_owner = elgg_get_page_owner_entity();

if (instanceof_category($entity)) {
	if ($entity->icontime) {
		$icon = '<span class="categories-category-icon">' . elgg_view('output/img', array(
					'src' => $entity->getIconURL('tiny')
				)) . '</span>';
	}
	$count = get_filed_items($entity->guid, array(
		'count' => true,
		'container_guids' => (HYPECATEGORIES_GROUP_CATEGORIES && elgg_instanceof($page_owner, 'group')) ? $page_owner->guid : null
	));

	$attr = '<span>' . elgg_echo('categories:category:title', array($entity->getDisplayName(), $count)) . '</span>';

	echo elgg_view('output/url', array(
		'text' => $icon . $attr,
		'href' => $entity->getURL(),
	));
} else if (elgg_instanceof($entity, 'site')) {
	echo '<span>' . elgg_echo('categories:site') . '</span>';
} else {
	echo '<span>'  . elgg_echo('categories:group', array($entity->name)) . '</span>';
}