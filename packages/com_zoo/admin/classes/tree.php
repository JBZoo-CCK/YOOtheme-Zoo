<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Base class to deal with tree structures
 *
 * @package Component.Classes
 */
class AppTree {

	/**
	 * A reference to the global App object
	 * 
	 * @var App
	 * @since 2.0
	 */
	public $app;

	/**
	 * The root node for the tree
	 * 
	 * @var object
	 * @since 2.0
	 */
	protected $_root;

	/**
	 * The class name
	 * 
	 * @var string
	 * @since 2.0
	 */
	protected $_itemclass;

	/**
	 * A list of filter methods to filter the tree
	 * 
	 * @var array
	 * @since 2.0
	 */
	protected $_filters = array();

	/**
	 * Class Constructor
	 * 
	 * @param string $itemclass The name of the class we're dealing with
	 * @since 2.0
	 */
	public function __construct($itemclass = null) {

		$this->app = App::getInstance('zoo');

		if ($itemclass == null) {
			$itemclass = get_class($this).'Item';
		}

		$this->_root = $this->app->object->create($itemclass);
		$this->_itemclass = $itemclass;
	}

	/**
	 * Add a tree filter
	 * 
	 * @param string $filter Method name
	 * @param array  $args   The list of arguments for the method
	 *
	 * @return AppTree $this for chaining support
	 * 
	 * @since 2.0
	 */
    public function addFilter($filter, $args = array()) {
		$this->_filters[] = compact('filter', 'args');
		return $this;
    }

	/**
	 * Execute the filters on all the tree
	 * 
	 * @return AppTree $this for chaining support
	 *
	 * @since 2.0
	 */
    public function applyFilter() {

		foreach ($this->_filters as $filter) {
			$this->_root->filter($filter['filter'], $filter['args']);
		}

		return $this;
    }

	/**
	 * Delegate the method calls to the AppTreeItem class
	 * 
	 * @param  string $method Method name
	 * @param  array  $args   List of arguments
	 * 
	 * @return mixed        Result of the method
	 *
	 * @since 2.0
	 */
    public function __call($method, $args) {
        return call_user_func_array(array($this->_root, $method), $args);
    }

}

/**
 * Class that represents an item in the tree
 *
 * @package Component.Classes
 */
class AppTreeItem {

	/**
	 * Reference to the global app object
	 * 
	 * @var App
	 * @since 2.0
	 */
	public $app;

	/**
	 * THe parent item
	 * 
	 * @var AppTreeItem
	 * @since 2.0
	 */
	protected $_parent;

	/**
	 * List of children
	 * 
	 * @var array
	 * @since 2.0
	 */
	protected $_children = array();

	/**
	 * Get the item unique id (object hash)
	 * 
	 * @return string Unique id
	 *
	 * @since 2.0
	 */
	public function getID() {
		return spl_object_hash($this);
	}

	/**
	 * Get the item parent
	 * 
	 * @return AppTreeItem The parent item
	 *
	 * @since 2.0
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * Set the parent item
	 * 
	 * @param AppTreeItem $item The menu item
	 *
	 * @return AppTreeItem $this for chaining support
	 *
	 * @since 2.0
	 */
	public function setParent($item) {
		$this->_parent = $item;
		return $this;
	}

	/**
	 * Get the children list
	 * 
	 * @return array The list of children
	 *
	 * @since 2.0
	 */
	public function getChildren() {
		return $this->_children;
	}

	/**
	 * Check if this item has a particular children
	 * 
	 * @param  string  $id        The item id to find
	 * @param  boolean $recursive If the search should go also through the children recursively (default: false)
	 * 
	 * @return boolean            True if the item is a children 
	 *
	 * @since 2.0
	 */
	public function hasChild($id, $recursive = false) {

		if (isset($this->_children[$id])) {
			return true;
		}

		if ($recursive) {
			foreach ($this->_children as $child) {
				if ($child->hasChild($id, $recursive)) return true;
			}
		}

		return false;
	}

	/**
	 * Count the children of the item
	 * 
	 * @return int The number of children
	 *
	 * @since 2.0
	 */
	public function hasChildren() {
		return count($this->_children);
	}

	/**
	 * Add a child to the item
	 * 
	 * @param AppTreeItem $item The item to add
	 *
	 * @return AppTreeItem $this for chaining support
	 * 
	 * @since 2.0
	 */
	public function addChild(AppTreeItem $item) {

		$item->setParent($this);
		$this->_children[$item->getID()] = $item;

		return $this;
	}

	/**
	 * Add a list of items to the item
	 * 
	 * @param array $children The list of items to add
	 *
	 * @return AppTreeItem $this for chaining support
	 *
	 * @since 2.0
	 */
	public function addChildren(array $children) {

		foreach ($children as $child) {
			$this->addChild($child);
		}

		return $this;
	}

	/**
	 * Remove a child
	 * 
	 * @param  AppTreeItem $item The child to remove
	 * 
	 * @return AppTreeItem            $this for chaining support
	 *
	 * @since 2.0
	 */
	public function removeChild(AppTreeItem $item) {

		$item->setParent(null);
		unset($this->_children[$item->getID()]);

		return $this;
	}

	/**
	 * Remove the item with the given id
	 * 
	 * @param  string $id The id of the item to remove
	 * 
	 * @return AppTreeItem     $this for chaining support
	 *
	 * @since 2.0
	 */
	public function removeChildById($id) {
		if ($this->hasChild($id)) {
			$this->removeChild($this->_children[$id]);
		}

		return $this;
	}

	/**
	 * Get the path from the current item to the root of the tree
	 * 
	 * @return array The pathway
	 *
	 * @since 2.0
	 */
	public function getPathway() {

		if ($this->_parent == null) {
			return array();
		}

		$pathway   = $this->_parent->getPathway();
		$pathway[] = $this;

		return $pathway;
	}

	/**
	 * Filter all the items recursiveluy
	 * 
	 * @param  function $callback A function to call
	 * @param  array    $args     A list of arguments to pass
	 * 
	 * @since 2.0
	 */
	public function filter($callback, $args = array()) {

		// call filter function
		call_user_func_array($callback, array_merge(array($this), $args));

		// filter all children
		foreach ($this->getChildren() as $child) {
			$child->filter($callback, $args);
		}
	}

}

/**
 * Exception for the AppTree class
 *
 * @see AppTree
 */
class AppTreeException extends AppException {}