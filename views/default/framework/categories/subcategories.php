<?php

namespace hypeJunction\Categories;

$container = elgg_extract('container', $vars);
$count = get_subcategories($container->guid, array('count' => true));

if (!$count) {
	return;
}

$title = elgg_echo('categories:subcategories');

$body = elgg_view('framework/categories/tree', $vars);

echo elgg_view_module('info', $title, $body, array(
	'class' => 'categories-subcategories-module'
));
