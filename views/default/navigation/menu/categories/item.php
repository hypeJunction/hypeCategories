<?php

/**
 * A single element of a menu.
 *
 * @package Elgg.Core
 * @subpackage Navigation
 */
$item = $vars['item'];

$link_class = 'elgg-menu-closed';
if ($item->getSelected()) {
	$item->setItemClass('elgg-state-selected');
	$link_class = 'elgg-menu-opened';
}

$children = $item->getChildren();
if ($children) {
	$item->addLinkClass($link_class);
	$item->addLinkClass('elgg-menu-parent');
}

$item_class = $item->getItemClass();
if (isset($vars['item_class']) && $vars['item_class']) {
	$item_class .= ' ' . $vars['item_class'];
}

$category_guid = $item->getData('category_guid');
$category = get_entity($category_guid);

if ($category) {
	$id = "hj-category-parent-" . $category->guid;
	$item_id = "elgg-object-$category->guid";
} else {
	$id = str_replace(':', '-', "{$item->getName()}");
	$id = explode('-', $id);
	$id = "hj-category-parent-" . end($id);
	$item_id = "elgg-object-" . end($id);
}

if (elgg_get_context() == 'category_input') {
	$item_class .= ' hj-category-input-item';
	$item->setHref("javascript:void(0)");
	if (in_array($category_guid, get_input('category', array()))) {
		$item->setSelected();
	}
}


if ($category && $category->canEdit() && elgg_get_context() == 'category' && !elgg_in_context('category_tree')) {
	$form = hj_framework_get_data_pattern('object', 'hjcategory');
	$params = array(
		'form_guid' => $form->guid,
		'container_guid' => $category->guid,
		'dom_order' => 'append',
		'full_view' => false,
		'list_type' => 'tree',
		'target' => $id,
		'fbox_x' => '900'
	);

	$category_menu = elgg_view_menu('hjentityhead', array(
		'entity' => $category,
		//'handler' => 'hjcategory',
		'sort_by' => 'priority',
		'params' => $params
			));
}
echo "<li id=\"$item_id\" class=\"elgg-item $item_class elgg-state-draggable clearfix\">";
echo $category_menu;
echo $item->getContent();
if ($children) {
	echo elgg_view('navigation/menu/categories/section', array(
		'items' => $children,
		'class' => 'elgg-menu elgg-child-menu',
		'id' => $id
	));
}
echo '</li>';
