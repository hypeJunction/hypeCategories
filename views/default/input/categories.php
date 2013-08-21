<?php

if (!isset($vars['multiple'])) {
	$vars['multiple'] = true;
}
echo elgg_view('input/category', $vars);
