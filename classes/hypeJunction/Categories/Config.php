<?php

namespace hypeJunction\Categories;

use hypeJunction\Categories\Category;

/**
 * Config
 * @property-read array $_context
 */
class Config extends \hypeJunction\Config {

	/**
	 * {@inheritdoc}
	 */
	public function getDefaults() {
		return array(
			'legacy_mode' => true,
			'relationship' => 'filed_in',
			'subtype' => Category::SUBTYPE,
			'entity_menu' => false,
			'group_categories' => false,
			'group_tree_site' => false,
			'input_multiple' => true,
			'pagehandler_id' => 'categories',
			'legacy_pagehandler_id' => 'category',
			'ajax_sidebar' => false,
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function all() {
		$settings = parent::all();
		$path = $this->getPath();
		$context = include("{$path}settings/default.php");
		$settings['_context'] = elgg_trigger_plugin_hook('get_context_settings', 'framework:categories', null, $context);
		return $settings;
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
		$pairs = $this->getEntityTypeSubtypePairs() ? : array();
		$subtypes = array();
		array_walk_recursive($pairs, function ($current) use (&$subtypes) {
			$subtypes[] = $current;
		});
		elgg_set_config('taxonomy_types', array_keys($pairs));
		elgg_set_config('taxonomy_subtypes', $subtypes);

		$tsp = array();
		foreach ($pairs as $type => $subtypes) {
			if (empty($subtypes)) {
				$subtypes = array('default');
			}
			foreach ($subtypes as $subtype) {
				$tsp[] = "$type:$subtype";
			}
		}

		elgg_set_config('taxonomy_type_subtype_pairs', $tsp);
		elgg_set_config('taxonomy_tree_subtypes', array(Category::SUBTYPE));
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

		$context = $this->getContextSettings();
		if (!empty($context['type_subtype_pairs']) && is_array($context['type_subtype_pairs'])) {
			return $context['type_subtype_pairs'];
		}
		
		$setting = $this->get('type_subtype_pairs');
		if ($setting) {
			$setting = unserialize($setting);
		}
		if (empty($setting)) {
			return get_registered_entity_types();
		}

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

		foreach ($pairs as $type => $subtypes) {
			if (empty($subtypes)) {
				$pairs[$type] = ELGG_ENTITIES_ANY_VALUE;
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
		$context = $this->getContextSettings();
		if (!empty($context['category_subtypes']) && is_array($context['subtypes'])) {
			$subtypes = array_unique(array_merge($subtypes, $context['category_subtypes']));
		}
		return elgg_trigger_plugin_hook('get_subtypes', 'framework:categories', null, $subtypes);
	}

	/**
	 * Matches current page URL against context settings and returns the config array
	 * @return array|false
	 */
	public function getContextSettings() {
		$contexts = (array) $this->_context;
		if (empty($contexts)) {
			return false;
		}

		$context = get_input('_context');
		if ($context) {
			return $contexts[$context];
		}
		
		$url = current_page_url();
		$site_url = elgg_get_site_url();

		foreach ($contexts as $context => $settings) {
			$pattern = "`^{$site_url}{$context}/*$`i";
			if (preg_match($pattern, $url)) {
				return $settings;
			}
		}

		return false;
	}

}
