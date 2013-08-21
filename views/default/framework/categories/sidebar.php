<?php

$container = elgg_get_page_owner_entity();

if (elgg_instanceof($container, 'user')) {
	// do not show on user owned pages
	return;
}

if (!elgg_instanceof($container, 'group')) {
	$container = elgg_get_site_entity();
} else if (!HYPECATEGORIES_GROUP_CATEGORIES) {
	return;
}

$count = hj_categories_get_subcategories($container->guid, array('count' => true));

if (!$count) {
	return;
}

$title = elgg_echo('hj:categories');

$vars['container'] = $container;
$body = elgg_view('framework/categories/tree', $vars);

if ($container->canEdit()) {
	$footer = elgg_view('output/url', array(
		'text' => elgg_echo('hj:categories:manage'),
		'href' => "categories/manage/$container->guid",
		'is_trusted' => true
	));
}
echo elgg_view_module('aside', $title, $body, array(
	'footer' => $footer,
	'class' => 'categories-sidebar-tree-module'
));