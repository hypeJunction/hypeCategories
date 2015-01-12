<?php

namespace hypeJunction\Categories;

$vars['name'] = preg_replace('/[^a-z0-9\-]/i', '-', $vars['name']);
$headers = elgg_extract('show_section_headers', $vars, false);

$class = "elgg-menu elgg-menu-{$vars['name']}";
if (isset($vars['class'])) {
	$class .= " {$vars['class']}";
}

foreach ($vars['menu'] as $section => $menu_items) {
	echo elgg_view('navigation/menu/categories/section', array(
		'items' => $menu_items,
		'class' => "$class elgg-menu-{$vars['name']}-$section",
		'section' => $section,
		'name' => $vars['name'],
		'show_section_headers' => $headers
	));
}
