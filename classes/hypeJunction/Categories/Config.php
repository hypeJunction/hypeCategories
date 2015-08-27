<?php

namespace hypeJunction\Categories;

use hypeJunction\Categories\Category;

/**
 * Config
 * @property-read array $_context
 */
class Config extends \hypeJunction\Config {

	private $contextCache = array();

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
		if (isset($this->settings)) {
			return $this->settings;
		}

		parent::all();
		if (!isset($this->settings['_context'])) {
			$path = $this->getPath();
			$context = include("{$path}settings/default.php");
			$this->settings['_context'] = elgg_trigger_plugin_hook('get_context_settings', 'framework:categories', null, $context);
		}

		return $this->settings;
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
			$pairs = get_registered_entity_types();
		} else {
			$pairs = array();

			foreach ($setting as $tsp) {
				list($type, $subtype) = explode(':', $tsp);
				if (!isset($pairs[$type])) {
					$pairs[$type] = array();
				}
				if ($subtype && $subtype !== 'default') {
					$pairs[$type][] = $subtype;
				}
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
		if (is_array($context)) {
			$subtypes = elgg_extract('category_subtypes', $context, $subtypes, false);
		}
		return elgg_trigger_plugin_hook('get_subtypes', 'framework:categories', null, $subtypes);
	}

	/**
	 * Matches current page URL against context settings and returns the config array
	 *
	 * @param string $match_url URL to match context against. Defaults to current page url
	 * @return array|false
	 */
	public function getContextSettings($match_url = null) {
		$contexts = (array) $this->_context;
		if (empty($contexts)) {
			return false;
		}

		foreach ($contexts as $context => $settings) {
			$settings['_context'] = $context;
		}

		$context = get_input('_context');
		if ($context) {
			return $contexts[$context];
		}

		$url = $match_url ? : hypeCategories()->router->getRealPageURL();
		if (!$url) {
			$url = current_page_url();
		}

		$url = parse_url($url, PHP_URL_PATH);

		if (isset($this->contextCache[$url])) {
			return $this->contextCache[$url];
		}

		foreach ($contexts as $context => $settings) {
			$regex = elgg_extract('regex', $settings);
			if (!$regex) {
				continue;
			}
			if (preg_match("/{$regex}/i", $url)) {
				$this->contextCache[$url] = $settings;
				return $settings;
			}
		}

		return false;
	}

}
