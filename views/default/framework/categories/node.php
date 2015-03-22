<?php

namespace hypeJunction\Categories;

if (elgg_in_context('categories-manage')) {
	echo elgg_view('framework/categories/node/manage', $vars);
} else if (elgg_in_context('categories-input')) {
	echo elgg_view('framework/categories/node/input', $vars);
} else {
	echo elgg_view('framework/categories/node/tree', $vars);
}
