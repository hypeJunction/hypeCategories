<?php

namespace hypeJunction\Categories;

$container_guid = get_input('container_guid');
$container = get_entity($container_guid);

if (!elgg_instanceof($container)) {
	$container = elgg_get_site_entity();
}

// Categories can only be contained by the site or a group
if (!elgg_instanceof($container, 'site') && !elgg_instanceof($container, 'group')) {
	return false;
}

// User should be able to edit an entity to add categories to it
if (!$container->canEdit()) {
	return false;
}

if (elgg_instanceof($container, 'group')) {
	elgg_set_page_owner_guid($container->guid);
	$title = elgg_echo('categories:group', array($container->name));
	$layout = 'one_sidebar';
	$shell = 'default';
} else {
	elgg_push_context('admin');
	$title = elgg_echo('categories:site');
	$layout = 'admin';
	$shell = 'admin';
}

$content = elgg_view_form('categories/manage', array(
	'enctype' => 'multipart/form-data',
		), array(
	'container' => $container
		));

$layout = elgg_view_layout($layout, array(
	'title' => $title,
	'content' => $content,
		));

echo elgg_view_page($title, $layout, $shell);
