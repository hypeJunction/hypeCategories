<?php

$guid = get_input('guid');
$entity = get_entity($guid);

if (!hypeCategories()->categories->instanceOfCategory($entity)) {
	forward('', '404');
}

hypeCategories()->navigation->pushBreadcrumbs($entity);

$title = elgg_echo('categories:category', array($entity->getDisplayName()));

$content = elgg_view_entity($entity, array(
	'full_view' => true
		));

$sidebar = elgg_view('framework/categories/filter', array(
	'entity' => $entity
		));

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'sidebar' => $sidebar,
	'filter' => false
		));

echo elgg_view_page($title, $layout);
