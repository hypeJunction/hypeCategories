<?php

$subtypes = array('hjcategory');

foreach ($subtypes as $subtype) {
	update_subtype('object', $subtype);
}