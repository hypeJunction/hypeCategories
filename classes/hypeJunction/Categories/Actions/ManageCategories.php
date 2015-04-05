<?php

namespace hypeJunction\Categories\Actions;

use ElggObject;
use hypeJunction\Categories\Controllers\Actions\Action;
use hypeJunction\Categories\Exceptions\InvalidEntityException;
use hypeJunction\Categories\Exceptions\PermissionsException;
use hypeJunction\Filestore\IconHandler;

/**
 * 'categories/manage' action
 */
final class ManageCategories extends Action {

	private $input_categories;
	private $hierarchy;
	private $root_guid;
	private $root;
	private $nodes;

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		$this->input_categories = get_input('categories');
		$this->hierarchy = json_decode(get_input('hierarchy', json_encode(array())), true);
		$this->root_guid = get_input('container_guid');
		$this->root = get_entity($this->root_guid);

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

		$config = $this->input_categories;

		foreach ($config['hierarchy'] as $key => $node_id) {

			$guid = (int) $config['guid'][$key];
			$title = $config['title'][$key];
			$desc = $config['description'][$key];
			$access_id = $config['access_id'][$key];
			$subtype = $config['subtype'][$key];

			$category = get_entity($guid);
			if (!$category) {
				$class = get_subtype_class('object', $subtype) ? : get_class(new ElggObject);
				$category = new $class();
				$category->subtype = ($subtype) ? $subtype : $this->config->get('subtype');
				$category->owner_guid = elgg_get_logged_in_user_guid();
				$category->container_guid = $this->root_guid;
			}

			if (!$category) {
				$this->nodes[$node_id] = false;
				continue;
			}

			if (!$title) {
				$category->delete();
				$this->nodes[$node_id] = false;
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
				if (is_callable('hypeFilestore')) {
					hypeFilestore()->iconFactory->create($category, $_FILES['categories']['tmp_name']['icon'][$key]);
				}
			}

			$this->nodes[$node_id] = $category;
		}

		foreach ($this->hierarchy as $key => $root) {
			$this->updateHierarchy($root['id'], $root['children']);
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
	function updateHierarchy($node_id, $children) {

		$category = $this->nodes[$node_id];

		if (!$category || empty($children)) {
			return;
		}

		foreach ($children as $child) {
			$child_node_id = $child['id'];
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

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return get_input('action');
	}

}
