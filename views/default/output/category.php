<?php

$entity = elgg_extract('entity', $vars, false);
$value = elgg_extract('value', $vars);
echo $entity->category;
if ($entity) {
	$current_categories = elgg_get_entities_from_relationship(array(
		'relationship' => 'filed_in',
		'relationship_guid' => $entity->guid,
		//'inverse_relationship' => true
			));
} elseif ($value) {
	$value = explode(',', $value);
	foreach ($value as $cat) {
		$current_categories[] = get_entity($cat);
	}
}

if (is_array($current_categories)) {
	echo '<div class="clearfix">';
	echo elgg_view_entity_list($current_categories, array(
		'list_type' => 'tags',
		//'size' => 'topbar',
		'gallery_class' => 'hj-categories-gallery',
		'limit' => 0,
		'full_view' => false
	));
	echo '</div>';
}
