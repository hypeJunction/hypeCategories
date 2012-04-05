<?php

elgg_load_js('hj.categories.base');
elgg_load_css('hj.categories.base');

$entity = elgg_extract('entity', $vars);
$list_type = elgg_extract('list_type', $vars);
$full = elgg_extract('full_view', $vars, false);

if (!elgg_instanceof($entity, 'object', 'hjcategory')) {
	return true;
}
if ($list_type == 'tree') {
	elgg_push_context('category');
	$items = $entity->getMenuItem();
	echo elgg_view('navigation/menu/categories/section', array('items' => $items, 'id' => "hj-category-parent-$entity->guid"));
	elgg_pop_context();
	return true;
}

if ($list_type == 'tags') {
	//$icon = elgg_view_entity_icon($entity, $vars['size']);
	echo elgg_view('output/url', array(
		'text' => $entity->title,
		'href' => $entity->getURL()
	));
	return true;
}

$form = hj_framework_get_data_pattern('object', 'hjcategory');
$fields = $form->getFields();

// Short View of the Entity
$title = elgg_view('output/url', array('text' => $entity->title, 'href' => $entity->getURL()));

$subtitle = $entity->description;

$params = elgg_clean_vars($vars);
$params = hj_framework_extract_params_from_entity($entity, $params);

if (!$full || (elgg_is_xhr() && !elgg_in_context('fancybox'))) {
	//$short_description = elgg_get_excerpt($entity->description);
	$icon = elgg_view_entity_icon($entity, 'tiny');
} else {
	$icon = elgg_view_entity_icon($entity, 'medium');
}

$params['target'] = "elgg-object-$entity->guid";
$params['fbox_x'] = '900';

$header_menu = elgg_view_menu('hjentityhead', array(
	'entity' => $entity,
	'current_view' => $full,
	'has_full_view' => false,
	'handler' => 'hjevent',
	'class' => 'elgg-menu-hz hj-menu-hz',
	'sort_by' => 'priority',
	'params' => $params
		));


//$types = get_input('types', null);
//$subtypes = get_input('subtypes', null);
//
//if ($types && !is_array($types)) {
//	$types = explode(',', $types);
//}
//
//if ($subtypes && !is_array($subtypes)) {
//	$subtypes = explode(',', $subtypes);
//}

$types = get_registered_entity_types();

foreach ($types as $type => $subtypes) {
	if (empty($subtypes)) {
		$subtypes = array('default');
	}
	foreach ($subtypes as $subtype) {
		if ($subtype == 'default') {
			$subtype == null;
		}
		$items = elgg_get_entities_from_relationship(array(
			'types' => $type,
			'subtypes' => $subtype,
			'limit' => 0,
			'relationship' => 'filed_in',
			'relationship_guid' => $entity->guid,
			'inverse_relationship' => true
				));
		$str = elgg_echo("item:$type:$subtype");
		$count = 0;
		if ($items) {
			$count = count($items);
		}
		if ($count > 0) {
			$stats[$str] = $count;
			$full_description .= elgg_view_module('aside', "$str ($count)", elgg_view_entity_list($items, array('full_view' => false)));
		}
	}
}

if (is_array($stats)) {
	foreach ($stats as $str => $count) {
		$stats_str .= elgg_view_image_block("$str: ", $count, array('class' => 'hj-left'));
	}
} else {
	$stats_str = elgg_echo('hj:categories:empty');
}

$stats_str = elgg_view_module('info', elgg_echo('hj:categories:category:stats'), $stats_str);

$params = array(
	'entity' => $entity,
	'title' => $title,
	'metadata' => $header_menu,
	'subtitle' => $subtitle,
	'content' => $short_description,
);

$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);
$summary = elgg_view_image_block($icon, $list_body);

echo "<div id=\"elgg-object-$entity->guid\">";
if (!$full || (elgg_is_xhr() && !elgg_in_context('fancybox'))) {
	echo $summary;
} else {
	$full_description = elgg_view_module('info', elgg_echo('hj:categories:category:items'), $full_description);
	$comments .= elgg_view_module('info', elgg_echo('comments'), elgg_view_comments($entity));
	echo $summary;
	
	echo elgg_view_layout('hj/dynamic', array(
		'grid' => array(8,4),
		'content' => array($full_description . $comments, $stats_str)
	));
}
echo '</div>';
