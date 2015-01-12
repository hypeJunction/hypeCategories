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

namespace hypeJunction\Categories;

if (isset($vars['entity'])) {
	$vars['categories'] = get_entity_categories($vars['entity']->guid);
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
if (!is_array($vars['categories'])) {
	$vars['categories'] = array($vars['categories']);
}
$vars['categories'] = array_filter($vars['categories']);

if (!count($vars['categories'])) {
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

$icon_class = elgg_extract('icon_class', $vars);
$list_items = '<li>' . elgg_view_icon('categories', $icon_class) . '</li>';

foreach ($vars['categories'] as $category) {

	if (!instanceof_category($entity)) {
		continue;
	}

	$children = get_subcategories($category->guid, array('count' => true));

	if ($children > 0) {
		continue;
	}

	$crumbs = array();
	$hierarchy = get_hierarchy($category->guid, false, true);
	foreach ($hierarchy as $h) {
		$crumbs[] = $h->getDisplayName();
	}

	$list_items .= "<li class=\"$item_class\">";
	$list_items .= elgg_view('output/url', array(
		'href' => $category->getURL(),
		'title' => implode(" &#8227; ", $crumbs),
		'text' => $category->getDisplayName(),
	));
	$list_items .= '</li>';
}

$list = <<<___HTML
<ul class="$list_class">
	$list_items
</ul>
___HTML;

echo $list;

