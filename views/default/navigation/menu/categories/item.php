<?php

$item = elgg_extract('item', $vars);
if (!$item instanceof ElggMenuItem) {
	return;
}

$guid = get_input('guid');
$context_entity = get_entity($guid);

$entity = $item->getData('entity');
$collapse = (bool) $item->getData('collapse');

$hierarchy = array();
if ($context_entity instanceof \hypeJunction\Categories\Category) {
	$hierarchy = hypeCategories()->categories->getHierarchy($context_entity, true, true);
} else if ($current instanceof \ElggEntity) {
	$categories = hypeCategories()->categories->getItemCategories($context_entity, array(), true);
	foreach ($categories as $guid) {
		$hierarchy[] = $guid;
	}
}

$item_class = array($item->getItemClass());

if (in_array($entity->guid, $hierarchy)) {
	$collapse = false;
	$item_class[] = 'elgg-menu-highlighted';
}

$children = $item->getChildren();

$submenu = '';
if ($children) {
	$item_class[] = "elgg-menu-parent";
	$item_class[] = ($collapse) ? 'elgg-menu-closed' : 'elgg-menu-open';

	$toggle = '<span class="elgg-child-menu-toggle"><span class="collapse ">&#9698;</span><span class="expand">&#9654;</span></span>';

	if (!empty($children)) {
		$submenu = elgg_view('navigation/menu/categories/section', array(
			'items' => $children,
			'class' => 'elgg-menu elgg-child-menu',
			'collapse' => true
		));
	}
} else {
	$item_class[] = "elgg-menu-nochildren";
	$toggle = '<span class="elgg-child-menu-indicator">&#9675;</span>';
}

if (elgg_in_context('categories-manage')) {
	if (!$submenu) {
		$submenu = elgg_format_element('ul', array(
			'class' => 'elgg-menu elgg-child-menu',
		));
	}
	$toggle = '';
}

if ($item->getSelected()) {
	$item_class[] = "elgg-state-selected";
}

if (isset($vars['item_class'])) {
	$item_class[] = $vars['item_class'];
}

echo elgg_format_element('li', array(
	'class' => $item_class,
	'data-guid' => $entity->guid,
	'data-href' => ($entity) ? $entity->getURL() : '',
		), $toggle . elgg_view_menu_item($item) . $submenu);

