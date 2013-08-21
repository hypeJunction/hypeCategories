<?php

$container = elgg_extract('container', $vars);

$categories = hj_categories_get_subcategories($container->guid);

echo elgg_view_entity_list($categories, array(
	'full_view' => false,
	'pagination' => false,
	'limit' => 0
));