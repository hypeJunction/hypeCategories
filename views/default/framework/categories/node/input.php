<?php

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof ElggEntity) {
	return;
}

$input_params = elgg_extract('input', $vars, array());
$name = elgg_extract('name', $input_params, 'categories');
$value = elgg_extract('value', $input_params, array());
$multiple = elgg_extract('multiple', $input_params, hypeCategories()->config->allowsMultipleInput());

if (hypeCategories()->categories->instanceOfCategory($entity)) {
	$has_children = hypeCategories()->categories->getSubcategories($entity, array('count' => true));
	$checkbox = elgg_format_element('input', array(
		'type' => ($multiple === false) ? 'radio' : 'checkbox',
		'name' => "{$name}[]",
		'value' => $entity->guid,
		'checked' => (is_array($value) && in_array($entity->guid, $value)),
		'class' => ($has_children) ? 'categories-tree-input-node' : 'categories-tree-input-leaf',
	));
	$title = $entity->getDisplayName();
} else if ($entity instanceof ElggSite) {
	$title = elgg_echo('categories:select:site');
} else if ($entity instanceof ElggGroup) {
	$title = elgg_echo('categories:select:group');
} else {
	$title = elgg_echo('categories:select');
}

$label = elgg_format_element('span', array(
	'class' => 'categories-tree-label',
		), $title);

echo elgg_format_element('label', array(
	'class' => 'categories-tree-node'
		), $checkbox . $label);
