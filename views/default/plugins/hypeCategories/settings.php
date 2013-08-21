<?php

$entity = elgg_extract('entity', $vars);

echo '<div>';
echo '<label>' . elgg_echo('hj:categories:group_categories') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[group_categories]',
	'value' => $entity->group_categories,
	'options_values' => array(
		true => elgg_echo('hj:categories:enable'),
		false => elgg_echo('hj:categories:disable')
	),
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('hj:categories:group_tree_site') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[group_tree_site]',
	'value' => $entity->group_tree_site,
	'options_values' => array(
		true => elgg_echo('hj:categories:enable'),
		false => elgg_echo('hj:categories:disable')
	),
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('hj:categories:entity_menu') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[entity_menu]',
	'value' => $entity->entity_menu,
	'options_values' => array(
		true => elgg_echo('hj:categories:display'),
		false => elgg_echo('hj:categories:donotdisplay')
	),
));
echo '</div>';