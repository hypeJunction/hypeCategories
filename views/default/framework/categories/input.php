<?php

$context = hypeCategories()->config->getContextSettings();
if (!$context) {
	return;
}

$forms = elgg_extract('forms', $context);
if (empty($forms)) {
	return;
}

$site_url = elgg_get_site_url();

foreach ($forms as $form) {
	$page_url = elgg_extract('page_url', $form);
	$selector = elgg_extract('selector', $form);

	if (!$page_url || !$selector) {
		continue;
	}

	$page_url = trim($page_url, '/') . '/';

	$pattern = preg_replace('/{([^{}]*)}/i', '(?<$1>.*)', $page_url);
	$pattern = str_replace('/', '\/', $pattern);

	$matches = array();
	preg_match("/$pattern/i", parse_url(current_page_url(), PHP_URL_PATH) . '/', $matches);

	if (!$matches) {
		continue;
	}

	$guid = elgg_extract('guid', $matches);
	$container_guid = elgg_extract('container_guid', $matches);

	$page_owner = elgg_get_page_owner_entity();
	$entity = get_entity($guid);
	$container = get_entity($container_guid);

	if ($entity) {
		$container = $entity->getContainerEntity();
	}

	if (!$container) {
		$container = $page_owner;
	}

	elgg_set_page_owner_guid($container->guid);

	elgg_require_js('framework/categories/inject');
	
	$input = elgg_view('input/category', array(
		'entity' => $entity,
	));
	echo elgg_format_element('div', array(
		'style' => 'display:none;',
		'data-inject' => 'categories-input',
		'data-selector' => $form['selector']
			), $input);

	elgg_set_page_owner_guid($page_owner->guid);

	break;
}
