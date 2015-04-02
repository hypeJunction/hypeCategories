<?php

use hypeJunction\Categories\Category;

require_once __DIR__ . '/vendor/autoload.php';

$subtypes = array(
	Category::SUBTYPE => Category::CLASSNAME,
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}