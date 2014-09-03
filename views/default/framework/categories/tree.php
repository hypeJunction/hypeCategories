<?php

namespace hypeJunction\Categories;

$container = elgg_extract('container', $vars);

echo '<div class="categories-tree">';
echo elgg_view_menu('categories', array(
	'entity' => $container,
	'sort_by' => 'priority',
));
echo '</div>';
