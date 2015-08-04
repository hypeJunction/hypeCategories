<?php

namespace hypeJunction\Categories;

use hypeJunction\Categories\Category;

/**
 * Config
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
		$pairs = $this->getEntityTypeSubtypePairs() ?: array();
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
