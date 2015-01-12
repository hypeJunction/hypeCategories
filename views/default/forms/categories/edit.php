<?php

namespace hypeJunction\Categories;

$entity = elgg_extract('entity', $vars);

if (elgg_instanceof($entity)) {
	$container = $entity->getContainerEntity();
} else {
	$container = elgg_extract('container', $vars, elgg_get_site_entity());
}

echo elgg_view('input/hidden', array(
	'name' => 'categories[hierarchy][]',
	'value' => ''
));

echo elgg_view('input/hidden', array(
	'name' => 'categories[guid][]',
	'value' => $entity->guid
));
echo elgg_view('input/hidden', array(
	'name' => 'categories[container_guid][]',
	'value' => $container->guid,
	'rel' => 'container-guid',
));

echo '<div class="categories-icon-move icon-small"></div>';

$upload = elgg_echo('categories:edit:icon');

if (elgg_instanceof($entity) && $entity->icontime) {
	$has_icon = true;
	$icon_upload_class = 'categories-has-icon';
}
echo "<div class=\"categories-icon-upload $icon_upload_class\" title=\"$upload\">";
echo elgg_view('input/file', array(
	'name' => 'categories[icon][]',
	'class' => 'hidden'
));
echo '</div>';

if ($has_icon) {
	echo elgg_view('output/img', array(
		'src' => $entity->getIconURL('tiny'),
		'class' => 'categories-icon-preview',
	));
}

echo '<div class="categories-category-title">';
echo elgg_view('input/text', array(
	'name' => 'categories[title][]',
	'value' => ($entity->title) ?: $entity->getDisplayName(),
	'placeholder' => elgg_echo('categories:edit:title')
));
echo '</div>';

echo '<div class="categories-icon-info icon-small"></div>';
echo '<div class="categories-icon-plus"></div>';
echo '<div class="categories-icon-minus"></div>';

echo '<div class="categories-category-meta hidden">';

$tree_subtypes = elgg_get_config('taxonomy_tree_subtypes');
$tree_subtype_options = array();
foreach ($tree_subtypes as $ts) {
	$tree_subtype_options[$ts] = elgg_echo("item:object:$ts");
}
if (count($tree_subtypes) > 1) {
	echo '<div class="categories-category-subtype">';
	echo elgg_view('input/dropdown', array(
		'name' => 'categories[subtype][]',
		'value' => HYPECATEGORIES_SUBTYPE,
		'options_values' => $tree_subtype_options,
		'disabled' => (elgg_instanceof($entity)),
	));
	echo '</div>';
}

echo '<div class="categories-category-description">';
//echo '<label>' . elgg_echo('categories:edit:description') . '</label>';
echo elgg_view('input/text', array(
	'name' => 'categories[description][]',
	'value' => $entity->description,
	'placeholder' => elgg_echo('categories:edit:description')
));
echo '</div>';

echo '<div class="categories-category-access">';
//echo '<label>' . elgg_echo('categories:edit:access_id') . '</label>';
echo elgg_view('input/access', array(
	'name' => 'categories[access_id][]',
	'entity' => $entity,
));
echo '</div>';

echo '</div>';
