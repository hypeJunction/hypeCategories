<?php

use hypeJunction\Categories\Category;

$subtypes = array(Category::SUBTYPE);

foreach ($subtypes as $subtype) {
	update_subtype('object', $subtype);
}