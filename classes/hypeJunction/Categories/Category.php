<?php

namespace hypeJunction\Categories;

use ElggObject;

class Category extends ElggObject {

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
	public function getDisplayName() {
		$src = "category:$this->title";
		$i18n = elgg_echo($src);
		if ($i18n == $src) {
			return parent::getDisplayName();
		}
		return $i18n;
	}

}
