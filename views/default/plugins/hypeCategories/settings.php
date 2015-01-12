<?php

namespace hypeJunction\Categories;

$entity = elgg_extract('entity', $vars);

// Type / subtype pairs that can be categorized
// Types and subtypes to rate
$dbprefix = elgg_get_config('dbprefix');
$data = get_data("SELECT type AS type, subtype AS subtype
								FROM {$dbprefix}entity_subtypes");

$types = array();
foreach ($data as $r) {
	$type = $r->type;
	$subtype = $r->subtype;

	$types[$type][] = $subtype;

	$str = elgg_echo("item:$type:$subtype");
	$subtype_options[$str] = "$type:$subtype";
}

if (!array_key_exists('user', $types)) {
	$str = elgg_echo("item:user");
	$subtype_options[$str] = "user:default";
}

if (!array_key_exists('group', $types)) {
	$str = elgg_echo("item:group");
	$subtype_options[$str] = "group:default";
}
echo '<div>';
echo '<label>' . elgg_echo('categories:type_subtype_pairs') . '</label>';
echo '<div class="elgg-text-help">' . elgg_echo('categories:type_subtype_pairs:help') . '</div>';
echo elgg_view('input/checkboxes', array(
	'name' => 'params[type_subtype_pairs]',
	'value' => elgg_get_config('taxonomy_type_subtype_pairs'),
	'options' => $subtype_options,
));
echo '</div>';

// Default 'multiple' parameter for categories input
echo '<div>';
echo '<label>' . elgg_echo('categories:input_multiple') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[input_multiple]',
	'value' => $entity->input_multiple,
	'options_values' => array(
		true => elgg_echo('categories:multiple'),
		false => elgg_echo('categories:single')
	),
));
echo '</div>';


echo '<div>';
echo '<label>' . elgg_echo('categories:group_categories') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[group_categories]',
	'value' => $entity->group_categories,
	'options_values' => array(
		true => elgg_echo('categories:enable'),
		false => elgg_echo('categories:disable')
	),
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('categories:group_tree_site') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[group_tree_site]',
	'value' => $entity->group_tree_site,
	'options_values' => array(
		true => elgg_echo('categories:enable'),
		false => elgg_echo('categories:disable')
	),
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('categories:entity_menu') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[entity_menu]',
	'value' => $entity->entity_menu,
	'options_values' => array(
		true => elgg_echo('categories:display'),
		false => elgg_echo('categories:donotdisplay')
	),
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('categories:num_display') . '</label>';
echo '<div class="elgg-text-help">' . elgg_echo('categories:num_display:help') . '</div>';
echo elgg_view('input/text', array(
	'name' => 'params[num_display]',
	'value' => $entity->num_display,
));
echo '</div>';
