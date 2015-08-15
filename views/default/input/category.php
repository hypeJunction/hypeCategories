<?php

/**
 * Category input field
 *
 * @uses $vars['name_override'] Override name attribute (only use if you are planning to attach custom logic to assigning categories to entities)
 * @uses $vars['value'] An array of category GUIDs that should be checked by default (you can leave this empty if you are passing $vars['entity']
 * @uses $vars['entity'] An entity, which is being edited
 * @uses $vars['multiple'] If set to true, input type will be set to checkbox, otherwise radio
 * @uses $vars['required'] For now, this will be ignored as HTML spec does not provide clear guidelines
 */
elgg_push_context('categories-input');

$entity = elgg_extract('entity', $vars, false);

$name = 'categories';
if (isset($vars['name_override'])) {
	$name = elgg_extract('name_override', $vars);
}

$label = elgg_extract('label', $vars, elgg_echo('categories'));
$multiple = elgg_extract('multiple', $vars, hypeCategories()->config->allowsMultipleInput());
$required = elgg_extract('required', $vars, true);

$container = elgg_get_page_owner_entity();

$value = (array) elgg_extract('value', $vars, array());
if ($entity instanceof ElggEntity) {
	$batch = hypeCategories()->categories->getItemCategories($entity, array(), true);
	foreach ($batch as $guid) {
		$value[] = $guid;
	}
	$container = $entity->getContainerEntity();
}

if (!elgg_instanceof($container, 'group') || !hypeCategories()->config->allowsGroupCategories()) {
	$container = elgg_get_site_entity();
}

echo elgg_view('input/hidden', array(
	'name' => "{$name}",
	'value' => true,
	'required' => $required
));

echo $label ? elgg_format_element('label', [], $label) : '';
echo '<div class="categories-input">';
echo elgg_view_menu('categories', array(
	'entity' => $container,
	'sort_by' => 'priority',
	'input' => array(
		'name' => $name,
		'value' => $value,
		'multiple' => $multiple,
	)
));
echo '</div>';

elgg_pop_context();
