<?php

$context = elgg_get_context();

if (!$disable_contexts = elgg_get_plugin_setting('tree:disable:contexts', 'hypeCategories')) {
    $disable_contexts = array();
}
if (in_array($context, $disable_contexts)) {
    return true;
}

$root = elgg_get_page_owner_entity();

if (!elgg_instanceof($root, 'group')) {
	$root = elgg_get_site_entity();
}

//if ($context == 'category') {
//	$root = get_entity(get_input('e'));
//	if (!elgg_instanceof($root, 'object', 'hjcategory')) {
//		return true;
//	}
//}

elgg_load_js('hj.framework.ajax');

//elgg_load_js('hj.framework.tree');
elgg_load_js('hj.categories.base');
elgg_load_css('hj.categories.base');

elgg_push_context('category_tree');

$menu = elgg_view_menu('hjcategories', array(
	'entity' => $root,
    'handler' => 'sidebar',
    'sort_by' => 'priority',
	'class' => 'elgg-menu-page',
));

elgg_pop_context();

if ($root->canEdit()) {
	$edit = elgg_view('output/url', array(
		'text' => elgg_echo('hj:categories:edit'),
		'href' => "category/edit/$root->guid",
		'class' => 'hj-right'
	));
}

$html = <<<HTML
    <div id="hj-categories-sidebar-tree" class="clearfix">
        $menu
		$edit
    </div>
HTML;

echo elgg_view_module('info', elgg_echo('hj:categories:categories'), $html);
