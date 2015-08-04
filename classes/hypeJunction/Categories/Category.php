<?php

namespace hypeJunction\Categories;

use ElggBatch;
use ElggObject;

/**
 * Category object
 *
 * @property int $priority
 */
class Category extends ElggObject {

	const CLASSNAME = __CLASS__;
	const SUBTYPE = 'hjcategory';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save() {
		if (!$this->priority) {
			$this->priority = 0;
		}
		return parent::save();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDisplayName() {
		$src = "category:$this->title";
		$i18n = elgg_echo($src);
		if ($i18n == $src) {
			return parent::getDisplayName();
		}
		return $i18n;
	}

	/**
	 * Returns items in this category
	 *
	 * @param array $options ege* options
	 * @return ElggBatch
	 */
	public function getItems(array $options = array()) {
		return hypeCategories()->categories->getItemsInCategory($this, $options);
	}

	/**
	 * Returns subcategories in this category
	 * 
	 * @param array $options ege* options
	 * @return ElggBatch
	 */
	public function getSubcategories(array $options = array()) {
		return hypeCategories()->categories->getSubcategories($this, $options);
	}
}
