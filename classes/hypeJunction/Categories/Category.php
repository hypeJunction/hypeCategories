<?php

namespace hypeJunction\Categories;

use ElggObject;

class Category extends ElggObject {

	const SUBTYPE = 'hjcategory';

	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

}
