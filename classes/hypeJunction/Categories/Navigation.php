<?php

namespace hypeJunction\Categories;

use ElggEntity;
use ElggGroup;
use ElggObject;
use ElggUser;
use hypeJunction\Categories\Config;
use hypeJunction\Categories\Categories;

class Navigation {

	private $config;
	private $router;
	private $categories;

	/**
	 * Constructor
	 *
	 * @param Config   $config   Config
	 * @param Router   $router   Router
	 * @param Categories $categories Categories lib
	 */
	public function __construct(Config $config, Router $router, Categories $categories) {
		$this->config = $config;
		$this->router = $router;
		$this->categories = $categories;
	}

	protected function getBreadcrumbs(ElggEntity$entity = null, array $breadcrumbs = array()) {

		foreach ($breadcrumbs as $breadcrumb) {
			if ($entity->guid == elgg_extract('guid', $breadcrumb)) {
				// In case we have circular containment of entities
				$entity = null;
			}
		}

		if ($entity instanceof ElggUser) {
			$temp = array(
				'guid' => $entity->guid,
				'text' => $entity->getDisplayName(),
				'href' => $this->router->normalize(array('owner', $entity->guid)),
			);
			array_unshift($breadcrumbs, $temp);
		} else if ($entity instanceof ElggGroup) {
			$temp = array(
				'guid' => $entity->guid,
				'text' => $entity->getDisplayName(),
				'href' => $this->router->normalize(array('group', $entity->guid)),
			);
			array_unshift($breadcrumbs, $temp);
		} else if ($entity instanceof ElggObject) {
			$temp = array(
				'guid' => $entity->guid,
				'text' => $entity->getDisplayName(),
				'href' => $entity->getURL(),
			);
			array_unshift($breadcrumbs, $temp);

			if ($entity instanceof ElggObject) {
				// Do not go up the tree once we have reached the user or group
				$container = $entity->getContainerEntity();
				if ($container) {
					$breadcrumbs = $this->getBreadcrumbs($container, $breadcrumbs);
				}
			}
		}

		array_shift($breadcrumbs, array(
			'text' => elgg_echo('categories'),
			'href' => $this->router->normalize('all'),
		));
		
		return $breadcrumbs;
	}

	public function pushBreadcrumbs(ElggEntity $entity) {
		$breadcrumbs = $this->getBreadcrumbs($entity);
		$settings = $this->config->getContextSettings();
		if (!empty($settings['breadcrumbs']) && is_array($settings['breadcrumbs'])) {
			$first = array_shift($breadcrumbs);
			$breadcrumbs = array_merge($settings['breadcrumbs'], $breadcrumbs);
			array_unshift($breadcrumbs, $first);
		}
		foreach ($breadcrumbs as $breadcrumb) {
			$text = elgg_extract('text', $breadcrumb, '');
			$href = elgg_extract('href', $breadcrumb);
			if ($text) {
				elgg_push_breadcrumb($text, $href);
			}
		}
	}

}
