<?php

elgg_load_js('hj.categories.base');
elgg_load_css('hj.categories.base');

if (elgg_is_xhr()) {
	echo '<script type="text/javascript">';
	echo elgg_view('js/hj/categories/base');
	echo '</script>';
}

$entity = elgg_extract('entity', $vars, false);
$name = elgg_extract('name', $vars, 'category');
$value = elgg_extract('value', $vars, false);

if (!$value && $entity) {
	$category = explode(',', $entity->$name);
	set_input('category', $category);
}

$page_owner = elgg_get_page_owner_entity();

if (!elgg_instanceof($page_owner, 'group')) {
	$page_owner = elgg_get_site_entity();
}

elgg_push_context('category_input');
$categories = elgg_view_menu('hjcategories', array(
	'entity' => $page_owner,
	'handler' => 'input',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-page elgg-menu-category-input-list'
		));
elgg_pop_context();

//echo '<label>' . elgg_echo('hj:categories:category') . '</label><br />';
echo $categories;

if ($category) {
	foreach ($category as $cat) {
		echo elgg_view('input/hidden', array(
			'name' => 'category_guids[]',
			'value' => $cat
		));
	}
}