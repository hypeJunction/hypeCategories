<?php

namespace hypeJunction\Categories;

if (!HYPECATEGORIES_GROUP_CATEGORIES) {
	return true;
}

$group_guid = get_input('group_guid');
$container = get_entity($group_guid);

$guid = get_input('guid');
$entity = get_entity($guid);

elgg_set_page_owner_guid($group_guid);

group_gatekeeper();

if (elgg_instanceof($container, 'group')) {
	elgg_push_breadcrumb(elgg_echo('groups'), 'groups');
	elgg_push_breadcrumb($container->getDisplayName(), $container->getURL());
} else {
	elgg_push_breadcrumb(elgg_echo('categories'));
}

if (!instanceof_category($entity)) {
	return false;
}

$crumbs = get_hierarchy($entity->guid, false);
if ($crumbs) {
	foreach ($crumbs as $crumb) {
		elgg_push_breadcrumb($crumb->getDisplayName(), $crumb->getURL());
	}
}
elgg_push_breadcrumb($entity->getDisplayName());

$title = elgg_echo('categories:category', array($entity->getDisplayName()));

$content = elgg_view_entity($entity, array(
	'full_view' => true
		));

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => false,
		));

echo elgg_view_page($title, $layout);
