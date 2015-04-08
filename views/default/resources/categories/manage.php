<?php

$guid = get_input('container_guid');
if (!$guid) {
	$guid = elgg_get_site_entity()->guid;
}

elgg_entity_gatekeeper($guid);
elgg_group_gatekeeper(true, $guid);

$container = get_entity($guid);

elgg_set_page_owner_guid($container->guid);

// User should be able to edit an entity to add categories to it
if (!$container->canEdit()) {
	forward('',  '403');
}

hypeCategories()->navigation->pushBreadcrumbs($container);

$title = elgg_echo('categories:manage');
$content = elgg_view_form('categories/manage', array(
	'enctype' => 'multipart/form-data',
		), array(
	'container' => $container
		));

$layout = elgg_view_layout('one_sidebar', array(
	'title' => $title,
	'content' => $content,
		));

echo elgg_view_page($title, $layout);
