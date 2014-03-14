<?php

namespace hypeJunction\Categories;

$entity = elgg_extract('entity', $vars);

$input_params = elgg_extract('input', $vars, array());
$name = elgg_extract('name', $input_params, 'categories');
$value = elgg_extract('value', $input_params, array());
$multiple = elgg_extract('multiple', $input_params, HYPECATEGORIES_INPUT_MULTIPLE);

if (elgg_instanceof($entity, 'object', HYPECATEGORIES_SUBTYPE)) {

	$children = get_subcategories($entity->guid, array('count' => true));

	if ($children == 0) {
		$checkbox_attr = elgg_format_attributes(array(
			'type' => ($multiple === false) ? 'radio' : 'checkbox',
			'name' => "{$name}[]",
			'value' => $entity->guid,
			'checked' => (is_array($value) && in_array($entity->guid, $value)),
		));
		$checkbox = "<input $checkbox_attr />";
	}
	$attr = $entity->title;
} else if (elgg_instanceof($entity, 'site')) {
	$attr = elgg_echo('categories:select:site');
} else {
	$attr = elgg_echo('categories:select:group');
}

echo '<label>' . $checkbox . $attr . '</label>';
