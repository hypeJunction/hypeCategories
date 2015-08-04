<?php

namespace hypeJunction\Categories\Actions;

use ElggEntity;
use ElggObject;
use hypeJunction\Controllers\Action;
use hypeJunction\Exceptions\InvalidEntityException;
use hypeJunction\Exceptions\PermissionsException;

/**
 * 'categories/manage' action
 *
 * @property int[]      $input_categories
 * @property array      $hierarchy
 * @property int        $root_guid
 * @property ElggEntity $root
 * @property Category[] $nodes
 */
final class ManageCategories extends Action {

	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		parent::setup();
		$this->input_categories = get_input('categories');
		$this->hierarchy = json_decode(get_input('hierarchy', json_encode(array())), true);
		$this->root_guid = get_input('container_guid');
		$this->root = get_entity($this->root_guid);
		$this->nodes = array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		if (!$this->root) {
			throw new InvalidEntityException("Entity with guid '$this->root_guid' does not exist");
		}

		if (!$this->root->canEdit()) {
			throw new PermissionsException("Not allowed to edit root object ($this->root_guid)");
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @tip: if you are extending the category form, use can access these values in the volatileData of the category
	 */
	public function execute() {

		$nodes = array();
		$config = $this->input_categories;

		foreach ($config['hierarchy'] as $key => $node_id) {
			
			$node_id = (int) $node_id;
			
			$guid = (int) $config['guid'][$key];
			$title = $config['title'][$key];
			$desc = $config['description'][$key];
			$access_id = $config['access_id'][$key];
			$subtype = $config['subtype'][$key];

			$category = get_entity($guid);
			if (!$category) {
				$class = get_subtype_class('object', $subtype) ? : get_class(new ElggObject);
				$category = new $class();
				$category->subtype = ($subtype) ? $subtype : hypeCategories()->config->get('subtype');
				$category->owner_guid = elgg_get_logged_in_user_guid();
				$category->container_guid = $this->root_guid;
			}

			if (!$category) {
				$nodes[$node_id] = false;
				continue;
			}

			if (!$title) {
				$category->delete();
				$nodes[$node_id] = false;
				continue;
			} else {
				$category->title = $title;
				$category->description = $desc;
				$category->access_id = $access_id;
				$category->priority = $key + 1; // we don't want 0

				$form_values = array();
				foreach ($config as $param_name => $entity_params) {
					$form_values[$param_name] = $entity_params[$key];
				}
				$category->setVolatileData('formValues', $form_values);
				$category->save();
			}

			if ($_FILES['categories']['error']['icon'][$key] == UPLOAD_ERR_OK) {
				hypeApps()->iconFactory->create($category, $_FILES['categories']['tmp_name']['icon'][$key]);
			}

			$nodes[$node_id] = $category;
		}

		$this->nodes = $nodes;

		foreach ($this->hierarchy as $key => $root) {
			$this->updateHierarchy((int) $root['id'], $root['children']);
		}

		$this->result->addMessage(elgg_echo('categories:manage:success'));
	}

	/**
	 * Updates category hierarchy
	 *
	 * @param int   $node_id  Node id
	 * @param array $children Children
	 * @return void
	 */
	protected function updateHierarchy($node_id, $children) {

		$category = $this->nodes[$node_id];

		if (!$category || empty($children)) {
			return;
		}

		foreach ($children as $child) {
			$child_node_id = (int) $child['id'];
			$child_children = $child['children'];

			$subcategory = $this->nodes[$child_node_id];

			if (!$subcategory) {
				continue;
			}

			elgg_log("Updating taxonomy; parent node id $node_id -> child node id $child_node_id", 'NOTICE');

			if ($category->guid == $subcategory->guid) {
				continue;
			}

			$old_container = $subcategory->container_guid;
			$subcategory->container_guid = $category->guid;
			$subcategory->setVolatileData('oldContainer', $old_container);
			$subcategory->save();

			$this->updateHierarchy($child_node_id, $child_children);
		}
	}

}
