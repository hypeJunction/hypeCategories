<?php

namespace hypeJunction\Categories;

$guid = get_input('container_guid');
$container = get_entity($guid);

if (!elgg_instanceof($container)) {
	$container = elgg_get_site_entity();
}

elgg_set_page_owner_guid($container->guid);

if (elgg_instanceof($container, 'group')) {
	elgg_push_breadcrumb(elgg_echo('groups'), 'groups');
	elgg_push_breadcrumb($container->getDisplayName(), $container->getURL());
	elgg_push_breadcrumb(elgg_echo('categories:group:all'));
} else {
	elgg_push_breadcrumb(elgg_echo('categories'));
}

$title = elgg_echo('categories');

$content = elgg_view('framework/categories/all', array(
	'container' => $container
		));

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => false,
		));

echo elgg_view_page($title, $layout);
