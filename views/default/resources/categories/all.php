<?php

$guid = get_input('container_guid');
if (!$guid) {
	$guid = elgg_get_site_entity()->guid;
}

elgg_entity_gatekeeper($guid);
elgg_group_gatekeeper(true, $guid);

$container = get_entity($guid);

elgg_set_page_owner_guid($container->guid);

hypeCategories()->navigation->pushBreadcrumbs($container);

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
