<?php

namespace hypeJunction\Categories;

use ElggBatch;
use ElggEntity;
use hypeJunction\Categories\Config;
use hypeJunction\Categories\Util\ItemCollection;
use stdClass;


/**
 * Convenince methods for retrieving classification information
 */
class Categories {

	const EGE = 'elgg_get_entities';
	const EGE_METADATA = 'elgg_get_entities_from_metadata';
	const EGE_RELATIONSHIP = 'elgg_get_entities_from_relationship';

	private $config;

	/**
	 * Constructor
	 * @param Config $config
	 */
	public function __construct(Config $config) {
		$this->config = $config;
	}

	/**
	 * Checks if $entity is a valid category
	 * 
	 * @param ElggEntity $entity Category
	 * @return bool
	 */
	public function instanceOfCategory(ElggEntity $entity = null) {
		if (!$entity instanceof ElggEntity) {
			return false;
		}
		if (!in_array($entity->getSubtype(), $this->config->getCategorySubtypes())) {
			return false;
		}
		return true;
	}

	/**
	 * Compares entity type/subtype against allowed pairs
	 * 
	 * @param ElggEntity $entity Entity
	 * @return bool
	 */
	public function isAllowed(ElggEntity $entity = null) {

		if (!$entity instanceof ElggEntity) {
			return false;
		}

		$type = $entity->getType();
		$subtype = $entity->getSubtype();

		$allowed_pairs = $this->config->getEntityTypeSubtypePairs();

		if (!array_key_exists($type, $allowed_pairs)) {
			return false;
		}

		$return = $subtype ? in_array($subtype, $allowed_pairs[$type]) : true;

		return $return;
	}

	/**
	 * Get first level subcategories for a given container
	 *
	 * @param mixed $containers Container or an array of containers
	 * @param array $options    ege* options
	 * @param bool  $as_guids   Only return guids
	 * @return ElggBatch
	 */
	public function getSubcategories($containers, $options = array(), $as_guids = false) {
		$options = $this->getSubcategoriesQueryOptions($containers, $options);
		return $this->getEntities($options, $as_guids);
	}

	/**
	 * Prepares ege* options for querying subcategories
	 *
	 * @param mixed $containers Containers (guids or entities)
	 * @param array $options    ege* options
	 * @return ElggBatch
	 */
	public function getSubcategoriesQueryOptions($containers, array $options = array()) {

		$dbprefix = elgg_get_config('dbprefix');
		$nid = elgg_get_metastring_id('priority');
		$defaults = array(
			'selects' => array('CAST(msv.string AS SIGNED) AS priority'),
			'types' => 'object',
			'subtypes' => $this->config->getCategorySubtypes(),
			'joins' => array(
				"JOIN {$dbprefix}metadata md ON md.entity_guid = e.guid AND md.name_id = $nid",
				"JOIN {$dbprefix}metastrings msv ON msv.id = md.value_id",
			),
			'order_by' => 'priority = 0, priority ASC',
		);

		$options = array_merge($defaults, $options);

		$options['container_guids'] = ItemCollection::create($containers)->guids();

		return $options;
	}

	/**
	 * Adds item to a category (and recursively to all of it's parent categories)
	 *
	 * @param ElggEntity $entity    Item
	 * @param ElggEntity $category  Category
	 * @param bool       $recursive Add recursively upwards
	 * @return array An array of category guids to which the item was added
	 */
	public function addItemToCategory(ElggEntity $entity, ElggEntity $category, $recursive = true) {
		$categories = ($recursive) ? $this->getHierarchy($category, true, true) : array($category->guid);
		$relationship = $this->config->get('relationship');
		foreach ($categories as $category_guid) {
			add_entity_relationship($entity->guid, $relationship, $category_guid);
		}
		return $categories;
	}

	/**
	 * Replaces entity categories
	 *
	 * @param ElggEntity $entity     Entity
	 * @param array      $categories New categories (guids or entities)
	 * @param array      $options    ege* options for retrieving current entity categories
	 * @return array
	 */
	public function setItemCategories(ElggEntity $entity, array $categories = array(), array $options = array()) {

		$input_categories = ItemCollection::create($categories)->guids();
		$future_categories = array();
		$current_categories_batch = $this->getItemCategories($entity, $options, true);

		foreach ($input_categories as $guid) {
			$category = get_entity($guid);
			if (!$category) {
				continue;
			}
			$universal_categories[] = $category->getDisplayName();
			$hierarchy = $this->getHierarchy($category, true, true);
			$future_categories = array_merge($future_categories, $hierarchy);
		}

		$current_categories = array();
		foreach ($current_categories_batch as $c) {
			$current_categories[] = $c->guid;
		}

		// Storing categories metadata for compatibility with categories plugin
		$entity->universal_categories = $universal_categories;

		$relationship = $this->config->get('relationship');
		$to_remove = array_diff($current_categories, $future_categories);
		$to_add = array_diff($future_categories, $current_categories);

		foreach ($to_remove as $guid) {
			remove_entity_relationship($entity->guid, $relationship, $guid);
		}

		foreach ($to_add as $guid) {
			add_entity_relationship($entity->guid, $relationship, $guid);
		}

		return $to_add;
	}

	/**
	 * Get entities filed under a category
	 *
	 * @param ElggEntity $category Category
	 * @param array      $options  ege* options
	 * @param bool       $as_guids Return only GUIDs
	 * @return ElggBatch
	 */
	public function getItemsInCategory(ElggEntity $category, $options = array(), $as_guids = false) {

		$defaults = array(
			'type_subtype_pairs' => $this->config->getEntityTypeSubtypePairs(),
			'relationship' => $this->config->get('relationship'),
			'inverse_relationship' => true
		);

		$options = array_merge($defaults, $options);
		$options['relationship_guid'] = (int) $category->guid;

		return $this->getEntities($options, $as_guids, self::EGE_RELATIONSHIP);
	}

	/**
	 * Get categories an entity is filed in
	 *
	 * @param ElggEntity $entity   Entity
	 * @param array      $options  Additional parameters to be passed to the getter function
	 * @param bool       $as_guids Return an array of GUIDs
	 * @return ElggBatch
	 */
	public function getItemCategories(ElggEntity $entity, $options = array(), $as_guids = false) {

		$defaults = array(
			'types' => 'object',
			'subtypes' => $this->config->getCategorySubtypes(),
			'reltionship' => $this->config->get('relationship'),
			'inverse_relationship' => false,
		);

		$options = array_merge($defaults, $options);
		$options['relationship_guid'] = (int) $entity->guid;

		return $this->getEntities($options, $as_guids, self::EGE_RELATIONSHIP);
	}

	/**
	 * Build an array of categories from top level parent category to the current category
	 *
	 * @param ElggEntity $category Leaf category
	 * @param bool       $as_guids Return an array of guids instead of objects
	 * @param bool       $self     Include current category
	 * @return array
	 */
	public function getHierarchy(ElggEntity $category, $as_guids = false, $self = false) {

		$hierarchy = array();
		$guids = array(); // prevent loops
		while ($this->instanceOfCategory($category) && !in_array($category->guid, $guids)) {
			$guids[] = $category->guid;
			$hierarchy[] = ($as_guids) ? $category->guid : $category;
			$category = $category->getContainerEntity();
		}
		if (!$self) {
			unset($hierarchy[0]);
		}
		return (sizeof($hierarchy)) ? array_reverse($hierarchy) : array();
	}

	/**
	 * Returns a batch of entities or an array of guids
	 *
	 * @param array    $options  ege* options
	 * @param bool     $as_guids Only return guids
	 * @param callable $ege      ege* callable
	 * @return ElggBatch|array
	 */
	protected function getEntities(array $options = array(), $as_guids = false, callable $ege = null) {

		if (!$ege) {
			$ege = self::EGE;
		}

		if (!is_callable($ege)) {
			return array();
		}

		if (!empty($options['count'])) {
			return call_user_func($ege, $options);
		}

		if ($as_guids) {
			$options['callback'] = array($this, 'rowToGUID');
		}

		return new ElggBatch($ege, $options);
	}

	/**
	 * Callback function for ege* to only return guids
	 * 
	 * @param stdClass $row DB row
	 * @return int
	 */
	public static function rowToGUID($row) {
		return (int) $row->guid;
	}

}
