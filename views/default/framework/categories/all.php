<?php

$container = elgg_extract('container', $vars);

$limit_setting = elgg_get_plugin_setting('num_display', 'hypeCategories');
$limit = (int)get_input('limit', (!is_null($limit_setting)) ? $limit_setting : 0);
$offset = (int)get_input('offset', 0);
$count = hj_categories_get_subcategories($container->guid, array(
	'count' => true,
		));

$categories = hj_categories_get_subcategories($container->guid, array(
	'limit' => $limit,
	'offset' => $offset,
		));

echo elgg_view_entity_list($categories, array(
	'full_view' => false,
	'pagination' => ($limit && $count > $limit),
	'count' => $count,
	'limit' => $limit,
	'offset' => $offset,
));