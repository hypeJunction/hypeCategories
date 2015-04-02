<?php

if (!isset($vars['multiple'])) {
	$vars['multiple'] = hypeCategories()->config->allowsMultipleInput();
}

echo elgg_view('input/category', $vars);
