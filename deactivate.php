<?php

$subtypes = array(
	'hjcategory' => ''
);

foreach ($subtypes as $subtype => $class) {
	update_subtype('object', $subtype);
}
