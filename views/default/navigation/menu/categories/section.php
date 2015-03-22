<?php

namespace hypeJunction\Categories;

$headers = elgg_extract('show_section_headers', $vars, false);
$class = elgg_extract('class', $vars, '');
$item_class = elgg_extract('item_class', $vars, '');
$id = elgg_extract('id', $vars);
$collapse = elgg_extract('collapse', $vars, false);

if ($headers) {
	$name = elgg_extract('name', $vars);
	$section = elgg_extract('section', $vars);
	echo '<h2>' . elgg_echo("menu:$name:header:$section") . '</h2>';
}

echo "<ul id=\"$id\" class=\"$class\">";
foreach ($vars['items'] as $menu_item) {
	echo elgg_view('navigation/menu/categories/item', array(
		'item' => $menu_item,
		'item_class' => $item_class,
		'collapse' => $collapse
	));
}
echo '</ul>';
