<?php

namespace hypeJunction\Categories;

if (!isset($vars['multiple'])) {
	$vars['multiple'] = HYPECATEGORIES_INPUT_MULTIPLE;
}
echo elgg_view('input/category', $vars);
