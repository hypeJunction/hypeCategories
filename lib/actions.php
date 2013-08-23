<?php

$path = elgg_get_plugins_path() . 'hypeCategories/actions/';

elgg_register_action('categories/manage', $path . 'categories/manage.php');
elgg_register_action('hypeCategories/settings/save', $path . 'settings/save.php', 'admin');