<?php

elgg_register_classes((dirname(dirname(__FILE__))) . '/classes/');

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