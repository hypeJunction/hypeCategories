<?php

namespace hypeJunction\Categories;

use hypeJunction\Categories\Category;
use hypeJunction\Categories\Config;

/**
 * Routing and page handling service
 */
class Router {

	protected $config;

	/**
	 * Constructor
	 * @param Config $config
	 */
	public function __construct(Config $config) {
		$this->config = $config;
	}

	/**
	 * Categories page handler
	 *
	 * /categories/all/[<container_guid>]
	 * /categories/manage/<container_guid>
	 * /categories/view/<guid>
	 * /categories/group/<group_guid>/<guid>
	 *
	 * @param array $page URL segments
	 * @return bool
	 */
	public function handlePages($page) {

		switch ($page[0]) {

			default :
			case 'all' :
				if (isset($page[1])) {
					set_input('container_guid', $page[1]);
				}
				echo elgg_view('resources/categories/all');
				return true;

			case 'manage' :
				set_input('container_guid', $page[1]);
				echo elgg_view('resources/categories/manage');
				return true;

			case 'view' :
				set_input('guid', $page[1]);
				echo elgg_view('resources/categories/view');
				return true;

			case 'group' :
				if (isset($page[2])) {
					forward($this->normalize(array('view', $page[2])));
				} else {
					forward($this->normalize(array('all', $page[1])));
				}
				break;

			case 'json' :
				elgg_set_viewtype('json');
				switch ($page[1]) {
					case 'nodes' :
						echo elgg_view('resources/categories/nodes');
						return true;
				}
				break;
		}

		return false;
	}

	/**
	 * Returns page handler ID
	 * @return string
	 */
	public function getPageHandlerId() {
		return hypeCategories()->config->get('pagehandler_id', 'categories');
	}

	/**
	 * Returns normalized category URL
	 * 
	 * @param Category $entity Category
	 * @return string
	 */
	public function getEntityUrl(Category $entity) {
		$friendly = elgg_get_friendly_title($entity->getDisplayName());

		$context = $this->config->getContextSettings();
		if (!empty($context['category_url'])) {
			$url = $context['category_url'];
			$params = array(
				'{page_owner_guid}' => elgg_get_page_owner_guid(),
				'{category_guid}' => $entity->guid,
				'{category_name}' => $friendly,
			);
			foreach ($params as $key => $val) {
				$url = preg_replace("/$key/i", $val, $url);
			}
			return $url;
		}


		$query = array();
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof \ElggGroup) {
			$query['container_guid'] = $page_owner->guid;
		}
		return $this->normalize(array('view', $entity->guid, $friendly), $query);
	}

	/**
	 * Route context specific category pages
	 *
	 * @param string $hook   "route"
	 * @param string $type   "all"
	 * @param mixed  $return Route params
	 * @param array  $params Hook params
	 * @return array
	 */
	public function routeCategoryContextPages($hook, $type, $return, $params) {

		$identifier = elgg_extract('identifier', $return);
		$segments = (array) elgg_extract('segments', $return);
		array_unshift($segments, $identifier);

		$route_url = implode('/', $segments) . '/';

		$contexts = (array) $this->config->_context;
		foreach ($contexts as $context => $settings) {
			$category_url = elgg_extract('category_url', $settings);
			if (!$category_url) {
				continue;
			}
			$category_url = trim($category_url, '/') . '/';

			$pattern = preg_replace('/{([^{}]*)}/i', '(?<$1>.*)', $category_url);
			$pattern = str_replace('/', '\/', $pattern);

			$matches = array();
			preg_match("/$pattern/i", $route_url, $matches);

			if (empty($matches['category_guid'])) {
				continue;
			}

			set_input('container_guid', elgg_extract('page_owner_guid', $matches));
			set_input('type_subtype_pairs', elgg_extract('type_subtype_pairs', $settings, $this->config->getEntityTypeSubtypePairs()));
			set_input('_context', $context);
			
			return array(
				'identifier' => $this->getPageHandlerId(),
				'segments' => array(
					'view',
					elgg_extract('category_guid', $matches),
				),
			);
		}
	}

	/**
	 * Prefixes the URL with the page handler ID and normalizes it
	 *
	 * @param mixed $url   URL as string or array of segments
	 * @param array $query Query params to add to the URL
	 * @return string
	 */
	public function normalize($url = '', $query = array()) {

		if (is_array($url)) {
			$url = implode('/', $url);
		}

		$url = implode('/', array($this->getPageHandlerId(), $url));

		if (!empty($query)) {
			$url = elgg_http_add_url_query_elements($url, $query);
		}

		return elgg_normalize_url($url);
	}

}
