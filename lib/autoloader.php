<?php

if (!is_callable('hypeApps')) {
	throw new Exception("hypeCategories requires hypeApps");
}

$path = dirname(dirname(dirname(dirname(__FILE__))));

if (!file_exists("{$path}/vendor/autoload.php")) {
	throw new Exception('hypeCategories can not resolve composer dependencies. Run composer install');
}

require_once "{$path}/vendor/autoload.php";

/**
 * Plugin container
 * @return \hypeJunction\Categories\Plugin
 */
function hypeCategories() {
	return \hypeJunction\Categories\Plugin::factory();
}