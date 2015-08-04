<?php

$container = elgg_extract('container', $vars);
if (!$container instanceof ElggEntity) {
	return;
}

$options = hypeCategories()->categories->getSubcategoriesQueryOptions($container);
$options['full_view'] = false;
$options['no_results'] = elgg_echo('categories:no_results');

echo elgg_list_entities($options);