<?php

$subtypes = array(
	'hjcategory' => 'hjCategory'
);

foreach ($subtypes as $subtype => $class) {
	update_subtype('object', $subtype);
}
