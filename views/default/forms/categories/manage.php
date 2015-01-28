<?php

namespace hypeJunction\Categories;

$container = elgg_extract('container', $vars, elgg_get_site_entity());

elgg_push_context('categories-manage');

echo '<div class="categories-manage">';
echo elgg_view_menu('categories', array(
	'entity' => $container,
	'sort_by' => 'priority'
));
echo elgg_view('input/hidden', array(
	'name' => 'container_guid',
	'value' => $container->guid
));
echo elgg_view('input/hidden', array(
	'id' => 'category-hierarchy',
	'name' => 'hierarchy',
	'value' => ''
));
echo elgg_view('input/submit', array(
	'value' => elgg_echo('save')
));
echo '</div>';

elgg_pop_context();
