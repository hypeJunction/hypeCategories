<?php

namespace hypeJunction\Categories;

$container = elgg_get_page_owner_entity();

if (elgg_instanceof($container, 'user')) {
	// do not show on user owned pages
	return;
}

if (!elgg_instanceof($container, 'group')) {
	$container = elgg_get_site_entity();
} else if (!HYPECATEGORIES_GROUP_CATEGORIES || $container->categories_enable == "no") {
	return;
}

$count = get_subcategories($container->guid, array('count' => true));

if (!$count && !$container->canEdit()) {
	return;
}

$title = elgg_echo('categories');

$vars['container'] = $container;
$body = elgg_view('framework/categories/tree', $vars);

if ($container->canEdit()) {
	$footer = elgg_view('output/url', array(
		'text' => elgg_echo('categories:manage'),
		'href' => "categories/manage/$container->guid",
		'is_trusted' => true
	));
}
echo elgg_view_module('aside', $title, $body, array(
	'footer' => $footer,
	'class' => 'categories-sidebar-tree-module'
));
