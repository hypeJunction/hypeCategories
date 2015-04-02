<?php

/**
 * Traverse Categories for Elgg
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2011-2015, Ismayil Khayredinov
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2
 */
require_once __DIR__ . '/lib/autoloader.php';

if (hypeCategories()->config->get('legacy_mode')) {
	require_once __DIR__ . '/functions.php';
	hypeCategories()->config->setLegacyConfig();
}

elgg_register_event_handler('init', 'system', function() {

	hypeCategories()->events->init();
	hypeCategories()->hooks->init();
	hypeCategories()->router->init();
	hypeCategories()->actions->init();

	elgg_define_js('jquery.nestedSortable', array(
		'src' => '/mod/hypeCategories/vendors/nestedSortable/jquery.mjs.nestedSortable.js',
		'deps' => array('jquery'),
		'exports' => 'jQuery.fn.nestedSortable',
	));

	elgg_define_js('jquery.jstree', array(
		'src' => '/mod/hypeCategories/vendors/jquery.jstree-3.1.0/jstree.min.js',
		'exports' => 'jQuery.fn.jstree',
		'deps' => array('jquery'),
	));
	elgg_register_css('jquery.jstree', '/mod/hypeCategories/vendors/jquery.jstree-3.1.0/themes/default/style.min.css');

	elgg_extend_view('css/elgg', 'css/framework/categories/stylesheet.css');
	elgg_extend_view('css/admin', 'css/framework/categories/stylesheet.css');

	elgg_extend_view('page/elements/sidebar', 'framework/categories/sidebar');

	elgg_register_ajax_view('framework/categories/subtree');

	if (hypeCategories()->config->allowsGroupCategories()) {
		add_group_tool_option('categories', elgg_echo('categories:groupoption:enable'), true);
	}
});
