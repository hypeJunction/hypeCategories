<?php

$container = elgg_get_page_owner_entity();

if (!$container) {
	$container = elgg_get_site_entity();
}

if ($container instanceof ElggUser) {
	// do not show on user owned pages
	return;
}

if ($container instanceof ElggGroup) {
	if (!hypeCategories()->config->allowsGroupCategories() || $container->categories_enable == "no") {
		return;
	}
}

$count = hypeCategories()->categories->getSubcategories($container, array('count' => true));

if (!$count && !$container->canEdit()) {
	return;
}

$title = elgg_echo('categories');

$vars['container'] = $container;
$body = elgg_view('framework/categories/tree', $vars);

if ($container->canEdit()) {
	$footer = elgg_view('output/url', array(
		'text' => elgg_echo('categories:manage'),
		'href' => hypeCategories()->router->normalize("manage/$container->guid"),
		'is_trusted' => true
	));
}
echo elgg_view_module('aside', $title, $body, array(
	'footer' => $footer,
	'class' => 'categories-sidebar-tree-module'
));
