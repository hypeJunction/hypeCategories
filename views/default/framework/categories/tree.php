<?php

elgg_load_js('categories.tree.js');
elgg_load_css('categories.base.css');

$container = elgg_extract('container', $vars);

echo '<div class="categories-tree">';
echo elgg_view_menu('categories', array(
	'entity' => $container,
	'sort_by' => 'priority',
));
echo '</div>';