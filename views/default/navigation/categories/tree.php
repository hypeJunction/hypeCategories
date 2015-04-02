<?php

elgg_require_js('framework/categories/tree');
elgg_load_css('jquery.jstree');

$container = elgg_extract('container', $vars);
if (!$container) {
	$container = elgg_get_site_entity();
}

$datasrc = elgg_extract('src', $vars);
if (!$datasrc) {
	$datasrc = hypeCategories()->router->normalize("all", array(
		'view' => 'json'
	));
}

$params = array(
	'class' => 'js-categories-dynamic-tree',
	'data-url' => $datasrc,
	'data-container-guid' => $container->guid,
);

foreach ($vars as $key => $value) {
	if (strpos($key, 'data-') !== false) {
		$params[$key] = $value;
	}
}

echo elgg_format_element('div', $params);
