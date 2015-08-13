<?php

$plugin_root = dirname(dirname(__FILE__));
if (file_exists("{$plugin_root}/vendor/autoload.php")) {
	// check if composer dependencies are distributed with the plugin
	require_once "{$plugin_root}/vendor/autoload.php";
}


/**
 * Plugin container
 * @return \hypeJunction\Categories\Plugin
 */
function hypeCategories() {
	return \hypeJunction\Categories\Plugin::factory();
}
