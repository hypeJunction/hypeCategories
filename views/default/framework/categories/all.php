<?php

$container = elgg_extract('container', $vars);
if (!$container instanceof ElggEntity) {
	return;
}

$options = hypeCategories()->model->getSubcategoriesQueryOptions($container);
$options['full_view'] = false;

echo elgg_list_entities($options);