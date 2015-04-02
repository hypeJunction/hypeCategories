<?php

namespace hypeJunction\Categories\Config;

use ElggPlugin;
use hypeJunction\Categories\Category;

/**
 * Config
 */
class Config {

	const PLUGIN_ID = 'hypeCategories';

	private $plugin;
	private $settings;
	private $config = array(
		'legacy_mode' => true,
		'relationship' => 'filed_in',
		'subtype' => Category::SUBTYPE,
		'entity_menu' => false,
		'group_categories' => false,
		'group_tree_site' => false,
		'input_multiple' => true,
		'pagehandler_id' => 'categories',
		'legacy_pagehandler_id' => 'category',
	);

	/**
	 * Constructor
	 * @param ElggPlugin $plugin ElggPlugin
	 */
	public function __construct(ElggPlugin $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * Config factory
	 * @return Config
	 */
	public static function factory() {
		$plugin = elgg_get_plugin_from_id(self::PLUGIN_ID);
		return new Config($plugin);
	}

	/**
	 * Initializes config values on system init
	 * @return void
	 */
	public function setLegacyConfig() {

		// legacy definitions
		define('HYPECATEGORIES_RELATIONSHIP', $this->get('relationship'));
		define('HYPECATEGORIES_SUBTYPE', $this->get('subtype'));
		define('HYPECATEGORIES_ENTITY_MENU', $this->get('entity_menu'));
		define('HYPECATEGORIES_GROUP_CATEGORIES', $this->get('group_categories'));
		define('HYPECATEGORIES_GROUP_TREE_SITE', $this->get('group_tree_site'));
		define('HYPECATEGORIES_INPUT_MULTIPLE', $this->get('input_multiple'));

		// legacy config values
		elgg_set_config('taxonomy_type_subtype_pairs', $pairs);
		$pairs = $this->getEntityTypeSubtypePairs();
		$subtypes = array();
		array_walk_recursive($pairs, function ($current) use (&$subtypes) {
			$subtypes[] = $current;
		});
		elgg_set_config('taxonomy_types', array_keys($pairs));
		elgg_set_config('taxonomy_subtypes', $subtypes);

		elgg_set_config('taxonomy_tree_subtypes', array(Category::SUBTYPE));
	}

	/**
	 * Returns all plugin settings
	 * @return array
	 */
	public function all() {
		if (!isset($this->settings)) {
			$this->settings = array_merge($this->config, $this->plugin->getAllSettings());
		}
		return $this->settings;
	}

	/**
	 * Returns a plugin setting
	 *
	 * @param string $name Setting name
	 * @return mixed
	 */
	public function get($name, $default = null) {
		return elgg_extract($name, $this->all(), $default);
	}

	/**
	 * Returns plugin path
	 * @return string
	 */
	public function getPath() {
		return $this->plugin->getPath();
	}

	/**
	 * Returns default category object subtype
	 * @return string
	 */
	public function getSubtype() {
		return $this->get('subtype');
	}

	/**
	 * Checks if group categories are allowed
	 * @return bool
	 */
	public function allowsGroupCategories() {
		return (bool) $this->get('group_categories', false);
	}

	/**
	 * Checks if categories should be shown in entity menu
	 * @return bool
	 */
	public function allowsCategoriesInMenu() {
		return (bool) $this->get('entity_menu', false);
	}

	/**
	 * Allows multiple selections in category input
	 * @return bool
	 */
	public function allowsMultipleInput() {
		return (bool) $this->get('input_multiple', true);
	}

	/**
	 * Returns type subtype pairs for ege* queries
	 * @return array
	 */
	public function getEntityTypeSubtypePairs() {

		$setting = $this->get('type_subtype_pairs');
		if (!$setting) {
			return get_registered_entity_types();
		}

		$setting = unserialize($setting);
		$pairs = array();

		foreach ($setting as $tsp) {
			list($type, $subtype) = explode(':', $tsp);
			if (!isset($pairs[$type])) {
				$pairs[$type] = array();
			}
			if ($subtype !== 'default') {
				$pairs[$type][] = $subtype;
			}
		}

		return $pairs;
	}

	/**
	 * Returns a filtered array of category subtypes
	 * @return array
	 */
	public function getCategorySubtypes() {
		$subtypes = elgg_get_config('taxonomy_tree_subtypes'); // keeping for legacy reasons
		if (!is_array($subtypes)) {
			$subtypes = array(Category::SUBTYPE);
		}
		return elgg_trigger_plugin_hook('get_subtypes', 'framework:categories', null, $subtypes);
	}

}
