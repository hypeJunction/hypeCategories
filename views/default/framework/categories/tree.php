<?php

if (hypeCategories()->config->get('ajax_sidebar')) {
	echo elgg_view('navigation/categories/tree', $vars);
} else {
	echo elgg_view('navigation/categories/menu', $vars);
}
