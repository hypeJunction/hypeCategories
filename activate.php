<?php

require_once __DIR__ . '/lib/autoloader.php';

$subtypes = array(
	\hypeJunction\Categories\Category::SUBTYPE => \hypeJunction\Categories\Category::CLASSNAME,
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}