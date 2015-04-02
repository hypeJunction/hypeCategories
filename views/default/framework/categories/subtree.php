<?php

$entity = elgg_extract('entity', $vars);

if (!$container instanceof ElggEntity) {
	return;
}

echo elgg_view_menu('categories', array(
	'entity' => $entity,
	'sort_by' => 'priority',
	'class' => 'elgg-child-menu',
));