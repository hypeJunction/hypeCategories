<?php

$category_guid = get_input('e');
$category = get_entity($category_guid);

if (!elgg_instanceof($category, 'object', 'hjcategory')) {
	forward();
}

//elgg_push_breadcrumb($category->title, $category->getURL());

$content = elgg_view_entity($category, array('full_view' => true));

$page = elgg_view_layout('one_sidebar', array('content' => $content));

echo elgg_view_page($category->title, $page);

