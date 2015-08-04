<?php

use hypeJunction\Categories\TreeNode;

$container_guid = get_input('container_guid');
$container = get_entity($container_guid);

if (!$container) {
	$container = elgg_get_site_entity();
}

$options = get_input('options', array('limit' => false));
$tree = new TreeNode($container, null, null, $options);

$nodes = array();
$children = $tree->getChildren();
foreach ($children as $child) {
	$category = $child->getEntity();
	if (!$category) {
		continue;
	}
	$nodes[] = array(
		'id' => $category->guid,
		'text' => $category->getDisplayName(),
		'icon' => ($category->icontime) ? $category->getIconURL('tiny') : false,
		'children' => $child->hasChildren(),
		'li_attr' => array(),
		'a_attr' => array(
			'href' => $category->getURL(),
		)
	);
}

echo json_encode($nodes);