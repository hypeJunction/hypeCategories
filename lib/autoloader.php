<?php

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

/**
 * Plugin DI Container
 * @staticvar \hypeJunction\Categories\Di\PluginContainer $provider
 * @return \hypeJunction\Categories\Di\PluginContainer
 */
function hypeCategories() {
	static $provider;
	if (null === $provider) {
		$provider = \hypeJunction\Categories\Di\PluginContainer::create();
	}
	return $provider;
}