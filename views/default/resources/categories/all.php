<?php

namespace hypeJunction\Categories;

$guid = get_input('container_guid');
$container = get_entity($guid);

if (!elgg_instanceof($container)) {
	$container = elgg_get_site_entity();
}

elgg_push_breadcrumb(elgg_echo('categories'));

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
