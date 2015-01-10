<?php

namespace hypeJunction\Categories;

require_once __DIR__ . '/vendors/autoload.php';

$subtypes = array(
	'hjcategory' => get_class(new Category),
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}