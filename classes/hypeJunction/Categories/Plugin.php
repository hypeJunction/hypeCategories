<?php

namespace hypeJunction\Categories;

/**
 * Categories service provider
 *
 * @property-read \ElggPlugin                            $plugin
 * @property-read \hypeJunction\Categories\Config        $config
 * @property-read \hypeJunction\Categories\HookHandlers  $hooks
 * @property-read \hypeJunction\Categories\Router	     $router
 * @property-read \hypeJunction\Categories\EventHandlers $events
 * @property-read \hypeJunction\Categories\Navigation    $navigation
 * @property-read \hypeJunction\Categories\Categories    $categories
 */
final class Plugin extends \hypeJunction\Plugin {

	/**
	 * {@inheritdoc}
	 */
	static $instance;

	/**
	 * {@inheritdoc}
	 */
	public function __construct(\ElggPlugin $plugin) {

		$this->setValue('plugin', $plugin);
		$this->setFactory('config', function (\hypeJunction\Categories\Plugin $p) {
			return new \hypeJunction\Categories\Config($p->plugin);
		});
		$this->setFactory('hooks', function (\hypeJunction\Categories\Plugin $p) {
			return new \hypeJunction\Categories\HookHandlers($p->config, $p->router, $p->categories);
		});
		$this->setFactory('events', function (\hypeJunction\Categories\Plugin $p) {
			return new \hypeJunction\Categories\EventHandlers($p->config, $p->router, $p->categories);
		});
		$this->setFactory('router', function (\hypeJunction\Categories\Plugin $p) {
			return new \hypeJunction\Categories\Router($p->config);
		});
		$this->setFactory('navigation', function (\hypeJunction\Categories\Plugin $p) {
			return new \hypeJunction\Categories\Navigation($p->config, $p->router, $p->categories);
		});
		$this->setFactory('model', function (\hypeJunction\Categories\Plugin $p) {
			return $p->categories;
		});
		$this->setFactory('categories', function(\hypeJunction\Categories\Plugin $p) {
			return new \hypeJunction\Categories\Categories($p->config);
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public static function factory() {
		if (null === self::$instance) {
			$plugin = elgg_get_plugin_from_id('hypeCategories');
			self::$instance = new self($plugin);
		}
		return self::$instance;
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {
		elgg_register_event_handler('init', 'system', array($this, 'init'));
	}

	/**
	 * System init callback
	 * @return void
	 */
	public function init() {

		if ($this->config->legacy_mode) {
			require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/lib/functions.php';
			$this->config->setLegacyConfig();
		}

		elgg_register_page_handler($this->config->pagehandler_id, array($this->router, 'handlePages'));
		if ($this->config->legacy_pagehandler_id) {
			elgg_register_page_handler($this->config->legacy_pagehandler_id, array($this->router, 'handlePages'));
		}

		elgg_register_plugin_hook_handler('entity:url', 'object', array($this->hooks, 'handleEntityUrls'));
		elgg_register_plugin_hook_handler('register', 'menu:categories', array($this->hooks, 'setupCategoriesMenu'));
		elgg_register_plugin_hook_handler('register', 'menu:category-filter', array($this->hooks, 'setupCategoryFilterMenu'));

		if ($this->config->allowsCategoriesInMenu()) {
			elgg_register_plugin_hook_handler('register', 'menu:entity', array($this->hooks, 'setupEntityMenu'));
		}

		elgg_register_event_handler('pagesetup', 'system', array($this->events, 'pagesetup'));
		elgg_register_event_handler('create', 'all', array($this->events, 'updateEntityCategories'));
		elgg_register_event_handler('update', 'all', array($this->events, 'updateEntityCategories'));


		$path = $this->plugin->getPath();
		elgg_register_action('categories/manage', $path . 'actions/categories/manage.php');
		elgg_register_action('hypeCategories/settings/save', $path . 'actions/settings/save.php', 'admin');

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

		if ($this->config->allowsGroupCategories()) {
			add_group_tool_option('categories', elgg_echo('categories:groupoption:enable'), true);
		}
	}

}
