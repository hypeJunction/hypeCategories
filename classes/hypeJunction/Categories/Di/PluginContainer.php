<?php

namespace hypeJunction\Categories\Di;

use Elgg\Di\DiContainer;
use hypeJunction\Categories\Config\Config;
use hypeJunction\Categories\Controllers\Actions;
use hypeJunction\Categories\Listeners\Events;
use hypeJunction\Categories\Listeners\PluginHooks;
use hypeJunction\Categories\Models\Model;
use hypeJunction\Categories\Services\Navigation;
use hypeJunction\Categories\Services\Router;
use hypeJunction\Categories\Services\Upgrades;

/**
 * Categories service provider
 *
 * @property-read Config      $config
 * @property-read Events      $events
 * @property-read PluginHooks $hooks
 * @property-read Router      $router
 * @property-read Actions     $actions
 * @property-read Navigation  $navigation
 * @property-read Model       $model
 * @property-read Upgrades    $upgrades
 */
final class PluginContainer extends DiContainer {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		$this->setFactory('config', '\hypeJunction\Categories\Config\Config::factory');
		
		$this->setFactory('events', function (PluginContainer $c) {
			return new Events($c->config, $c->router, $c->model, $c->upgrades);
		});

		$this->setFactory('hooks', function (PluginContainer $c) {
			return new PluginHooks($c->config, $c->router, $c->model);
		});
		
		$this->setFactory('router', function (PluginContainer $c) {
			return new Router($c->config);
		});

		$this->setFactory('actions', function (PluginContainer $c) {
			return new Actions($c->config, $c->router, $c->model);
		});

		$this->setFactory('navigation', function (PluginContainer $c) {
			return new Navigation($c->config, $c->router, $c->model);
		});

		$this->setFactory('model', function (PluginContainer $c) {
			return new Model($c->config);
		});

		$this->setFactory('upgrades', function (PluginContainer $c) {
			return new Upgrades($c->config, $c->model);
		});

	}

	/**
	 * Creates a new  ServiceProvider instance
	 * @return PluginContainer
	 */
	public static function create() {
		return new PluginContainer();
	}

}
