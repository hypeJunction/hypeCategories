<?php

/**
 * Categories
 *
 * @uses $vars['value']   Array of category GUIDs
 * @uses $vars['type']    The entity type, optional
 * @uses $vars['subtype'] The entity subtype, optional
 * @uses $vars['entity']  Optional. Entity whose categories are being displayed
 * @uses $vars['list_class'] Optional. Additional classes to be passed to <ul> element
 * @uses $vars['item_class'] Optional. Additional classes to be passed to <li> elements
 */

if (isset($vars['entity'])) {
	$vars['categories'] = hypeCategories()->categories->getItemCategories($vars['entity']);
	unset($vars['entity']);
}

if (!empty($vars['type'])) {
	$type = "&type=" . rawurlencode($vars['type']);
} else {
	$type = "";
}
if (!empty($vars['subtype'])) {
	$subtype = "&subtype=" . rawurlencode($vars['subtype']);
} else {
	$subtype = "";
}

if (empty($vars['categories']) && !empty($vars['value'])) {
	if (!is_array($vars['value'])) {
		$vars['value'] = string_to_tag_array($vars['value']);
	}
	foreach ($vars['value'] as $guid) {
		$vars['categories'][] = get_entity($guid);
	}
}

if (empty($vars['categories'])) {
	return;
}

$list_class = "elgg-categories";
if (isset($vars['list_class'])) {
	$list_class = "$list_class {$vars['list_class']}";
}

$item_class = "elgg-category";
if (isset($vars['item_class'])) {
	$item_class = "$item_class {$vars['item_class']}";
}

$list_items = array();

foreach ($vars['categories'] as $category) {

	if (!hypeCategories()->categories->instanceOfCategory($category)) {
		continue;
	}

	$children = hypeCategories()->categories->getSubcategories($category, array('count' => true));

	if ($children > 0) {
		continue;
	}

	$crumbs = array();
	$hierarchy = hypeCategories()->categories->getHierarchy($category, false, true);
	foreach ($hierarchy as $h) {
		$crumbs[] = $h->getDisplayName();
	}

	$list_items[] = elgg_format_element('li', array(
		'class' => $item_class,
	), elgg_view('output/url', array(
		'href' => $category->getURL(),
		'title' => implode(" &#8227; ", $crumbs),
		'text' => $category->getDisplayName(),
	)));
}

if (empty($list_items)) {
	return;
}

$icon = elgg_format_element('li', array(), elgg_view_icon('categories', $icon_class));
$list = implode('', $list_items);

echo elgg_format_element('ul', array(
	'class' => $list_class,
), $icon . $list);

