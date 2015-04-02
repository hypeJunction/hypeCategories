<?php

namespace hypeJunction\Categories\Controllers;

use hypeJunction\Categories\Config\Config;
use hypeJunction\Categories\Controllers\Actions\Action;
use hypeJunction\Categories\Controllers\Actions\ActionResult;
use hypeJunction\Categories\Exceptions\ActionValidationException;
use hypeJunction\Categories\Exceptions\Exception;
use hypeJunction\Categories\Exceptions\InvalidEntityException;
use hypeJunction\Categories\Exceptions\PermissionsException;
use hypeJunction\Categories\Models\Model;
use hypeJunction\Categories\Services\Router;

/**
 * Actions service
 */
class Actions {

	private $config;
	private $router;
	private $model;

	/**
	 * Constructor
	 *
	 * @param Config   $config     Config
	 * @param Router   $router     Router
	 * @param Model    $model      Taxonomy
	 */
	public function __construct(Config $config, Router $router, Model $model) {
		$this->config = $config;
		$this->router = $router;
		$this->model = $model;
	}

	/**
	 * Performs tasks on system init
	 * @return void
	 */
	public function init() {

		$path = $this->config->getPath() . 'actions/';
		elgg_register_action('categories/manage', $path . 'categories/manage.php');
		elgg_register_action('hypeCategories/settings/save', $path . 'settings/save.php', 'admin');
	}

	/**
	 * Executes an action
	 * Triggers 'action:after', $name hook that allows you to filter the Result object
	 * 
	 * @param Action $action   Action
	 * @param bool   $feedback Display errors and messages
	 * @return ActionResult
	 */
	public function execute(Action $action, $feedback = true) {

		elgg_make_sticky_form($name);

		$result = $action->getResult();

		try {
			if ($action->validate()) {
				$action->execute();
			}
			$result = $action->getResult();
		} catch (ActionValidationException $ex) {
			$result->addError(elgg_echo('categories:validation:error'));
		} catch (PermissionsException $ex) {
			$result->addError(elgg_echo('categories:permissions:error'));
		} catch (InvalidEntityException $ex) {
			$result->addError(elgg_echo('categories:entity:error'));
		} catch (Exception $ex) {
			$result->addError(elgg_echo('categories:action:error'));
		}

		$errors = $result->getErrors();
		$messages = $result->getMessages();
		if (empty($errors)) {
			elgg_clear_sticky_form($name);
		} else {
			$result->setForwardURL(REFERRER);
		}

		if ($feedback) {
			foreach ($errors as $error) {
				register_error($error);
			}
			foreach ($messages as $message) {
				system_message($message);
			}
		}

		return elgg_trigger_plugin_hook('action:after', $name, null, $result);
	}

}
