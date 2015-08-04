<?php

namespace hypeJunction\Categories;

use ElggEntity;
use hypeJunction\Categories\Util\ItemCollection;

class TreeNode {

	protected $node;
	protected $parent;
	protected $callback;
	protected $options;

	/**
	 * Constructor
	 *
	 * @param ElggEntity|int $node     ElggEntity or GUID
	 * @param TreeNode       $parent   Node parent
	 * @param callable       $callback Callback for populating child elements
	 *                                 The callback should accept $entity, $options as arguments
	 *                                 * return an array of guids or entities, or an ElggBatch
	 *                                 * return an integer if $options['count'] = true
	 * @param array          $options  ege* options for children querying
	 */
	public function __construct($node = 0, TreeNode $parent = null, callable $callback = null, array $options = array()) {
		$this->node = $node;
		$this->parent = $parent;
		$this->callback = $callback;
		$this->options = $options;
	}

	/**
	 * Returns the node entity
	 * @return ElggEntity
	 */
	public function getEntity() {
		return ($this->node instanceof ElggEntity) ? $this->node : get_entity($this->node);
	}

	/**
	 * Returns the guid of the node
	 * @return int
	 */
	public function getGUID() {
		return is_object($this->node) ? $this->node->guid : (int) $this->node;
	}

	/**
	 * Returns parent node
	 * @return TreeNode
	 */
	public function getParent() {
		return ($this->parent instanceof TreeNode) ? $this->parent : new TreeNode($this->parent);
	}

	/**
	 * Checks if node is a child of another node
	 * @return bool
	 */
	public function isChild() {
		return ($this->getParent());
	}

	/**
	 * Returns children nodes
	 * @return TreeNode[]
	 */
	public function getChildren() {
		$nodes = array();

		if (!$this->hasChildren()) {
			return $nodes;
		}

		$callback = $this->callback;

		if (is_callable($callback)) {
			$children = call_user_func($callback, $this->getEntity(), $this->options);
		} else {
			$children = hypeCategories()->categories->getSubcategories($this->getEntity(), $this->options, true);
		}
		
		if (is_array($children)) {
			$children = ItemCollection::create($children)->guids();
		}
		
		foreach ($children as $child) {
			$nodes[] = new TreeNode($child, $this, $this->callback, $this->options);
		}

		return $nodes;
	}

	/**
	 * Checks if node has children
	 * @return bool
	 */
	public function hasChildren() {
		$options = $this->options;
		$options['count'] = true;
		$callback = $this->callback;
		if (is_callable($callback)) {
			$children = call_user_func($callback, $this->getEntity(), $options);
		} else {
			$children = hypeCategories()->categories->getSubcategories($this->getEntity(), $options);
		}
		return (bool) $children;
	}

	/**
	 * Returns the depth of this node
	 * @return int
	 */
	public function getDepth() {
		$depth = 1; // self
		$children_depths = array(0);
		$children = $this->getChildren();
		foreach ($children as $child) {
			$children_depths[] = $child->getDepth();
		}
		return $depth + max($children_depths);
	}

	/**
	 * Returns this node and children nodes (if depth hasn't been reached)
	 * as $node_guid => $parent_guid pairs
	 *
	 * @param int|false $depth Limit the depth of the tree (false for  no limit)
	 * @return array
	 */
	public function toArray($depth = false) {
		$nodes = array(
			array(
				'node_guid' => $this->getGUID(),
				'parent_guid' => $this->getParent()->getGUID(),
				'has_children' => $this->hasChildren(),
			)
		);

		if ($depth === false || $depth > 0) {
			$children = $this->getChildren();
			foreach ($children as $child) {
				$next_depth = ($depth === false) ? false : $depth-1;
				$child_node = $child->toArray($next_depth);
				$nodes = array_merge($nodes, $child_node);
			}
		}

		return array_unique($nodes, SORT_REGULAR);
	}

}
