<?php

elgg_load_js('hj.framework.ajax');

//elgg_load_js('hj.framework.tree');
elgg_load_js('hj.categories.base');
elgg_load_css('hj.categories.base');

$parent_guid = get_input('parent', elgg_get_site_entity()->guid);
$parent = get_entity($parent_guid);

$menu = elgg_view_menu('hjcategories', array(
    //'handler' => 'sidebar',
    'sort_by' => 'priority',
	'entity' => $parent,
	'class' => 'hj-menu-category-page'
));

$html = <<<HTML
    <div id="hj-categories-page-tree" class="clearfix">
        $menu
    </div>
HTML;

$category_list = elgg_view_module('aside', elgg_echo('hj:categories:categories'), $html);

$layout = elgg_view_layout('one_sidebar', array(
		'content' => $category_list
		));

echo elgg_view_page(elgg_echo('hj:categories:categories'), $layout);
