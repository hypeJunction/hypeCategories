<?php

elgg_require_js('framework/categories/tree');
elgg_load_css('jquery.jstree');

$container = elgg_extract('container', $vars);

$menu = elgg_view_menu('categories', array(
	'entity' => $container,
	'sort_by' => 'priority',
		));

echo elgg_format_element('div', array(
	'class' => 'categories-tree',
		), $menu);
