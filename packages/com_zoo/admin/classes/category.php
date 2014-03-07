<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class representing a category
 *
 * @package Component.Classes
 */
class Category {

    /**
     * The category id
     *
     * @var int
     * @since 2.0
     */
	public $id;

    /**
     * Id of the category application
     *
     * @var int
     * @since 2.0
     */
	public $application_id;

    /**
     * The category name
     *
     * @var string
     * @since 2.0
     */
	public $name;

    /**
     * The category alias
     *
     * @var string
     * @since 2.0
     */
	public $alias;

    /**
     * The category description
     *
     * @var string
     * @since 2.0
     */
	public $description;

    /**
     * The id of the parent category
     *
     * @var int
     * @since 2.0
     */
	public $parent;

    /**
     * The ordering value for this column
     *
     * @var int
     *
     * @since 2.0
     */
	public $ordering;

    /**
     * If the category is published
     *
     * @var boolean
     *
     * @since 2.0
     */
	public $published;

    /**
     * Category parameters
     *
     * @var ParameterData
     *
     * @since 2.0
     */
	public $params;

    /**
     * The items ids of the category
     *
     * @var array
     * @since 2.0
     */
	public $item_ids;

    /**
     * A reference to the global App object
     *
     * @var App
     * @since 2.0
     */
	public $app;

    /**
     * The parent Category object
     *
     * @var Category
     * @since 2.0
     */
	protected $_parent;

    /**
     * List of children categories
     *
     * @var array
     * @since 2.0
     */
	protected $_children = array();

    /**
     * List of items in the category
     *
     * @var array
     * @since 2.0
     */
	protected $_items = array();

    /**
     * Item count for the category
     *
     * @var int
     * @since 2.0
     */
	protected $_item_count;

	/**
	 * Total item count including subcategories
	 *
	 * @var int
	 * @since 2.0
	 */
	protected $_total_item_count = null;

	/**
	 * Class Constructor
	 */
	public function  __construct() {

		// init vars
		$app = App::getInstance('zoo');

		// decorate data as object
		$this->params = $app->parameter->create($this->params);

		// set related item ids
		$this->item_ids = isset($this->item_ids) ? explode(',', $this->item_ids) : array();
		if (!empty($this->item_ids)) {
			$this->item_ids = array_combine($this->item_ids, $this->item_ids);
		}
	}

	/**
	 * Get the application
	 *
	 * @return Application The application to which the category belongs
	 *
	 * @since 2.0
	 */
	public function getApplication() {
		return $this->app->table->application->get($this->application_id);
	}

	/**
	 * If the category has child categories or not
	 *
	 * @return boolean If the category has child categories
	 *
	 * @since 2.0
	 */
	public function hasChildren() {
		return !empty($this->_children);
	}

	/**
	 * Get the category children categories
	 *
	 * @param boolean $recursive If the search should be recursive
	 *
	 * @return array The list of children
	 *
	 * @since 2.0
	 */
	public function getChildren($recursive = false) {

		if ($recursive) {
			$children = array();

			foreach ($this->_children as $child) {
				$children[$child->id] = $child;
				$children 			 += $child->getChildren(true);
			}

			return $children;
		}

		return $this->_children;
	}

	/**
	 * Set the children of the category
	 *
	 * @param array $val The list of children
	 *
	 * @return Category $this for chaining support
	 */
	public function setChildren($val) {
		$this->_children = $val;
		return $this;
	}

	/**
	 * Add a child to the children list
	 *
	 * @param Category $child The child to add
	 *
	 * @return Category $this for chaining support
	 *
	 * @since 2.0
	 */
	public function addChild($child) {
		$this->_children[$child->id] = $child;
		return $this;
	}

	/**
	 * Remove a child from the list of children
	 *
	 * @param Category $child The child to remove
	 *
	 * @return Category $this for chaining support
	 *
	 * @since 2.0
	 */
	public function removeChild($child) {
		unset($this->_children[$child->id]);
		return $this;
	}

	/**
	 * get the category parent category
	 *
	 * @return Category The category parent
	 *
	 * @since 2.0
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * Set the category parent category
	 *
	 * @param Category $val THe parent category
	 *
	 * @return Category $this for chaining support
	 *
	 * @since 2.0
	 */
	public function setParent($val) {
		$this->_parent = $val;
		return $this;
	}

	/**
	 * Get the category pathway
	 *
	 * @return array The pathway to this category
	 *
	 * @since 2.0
	 */
	public function getPathway() {
		if ($this->_parent == null) {
			return array();
		}

		$pathway   = $this->_parent->getPathway();
		$pathway[$this->id] = $this;

		return $pathway;
	}

	/**
	 * Check if a category is published
	 *
	 * @return boolean If the category is published
	 *
	 * @since 2.0
	 */
	public function isPublished() {
		return $this->published;
	}

	/**
	 * Check if a category is published
	 *
	 * @param boolean $val The new published state
	 * @param boolean $save If the change should be saved to the database. Default: false
	 *
	 * @return Category $this for chaining support
	 *
	 * @since 2.0
	 */
	public function setPublished($val, $save = false) {

		if ($this->published != $val) {

			// set state
			$old_state   = $this->published;
			$this->published = $val;

			// autosave category ?
			if ($save) {
				$this->app->table->category->save($this);
			}

			// fire event
		    $this->app->event->dispatcher->notify($this->app->event->create($this, 'category:stateChanged', compact('old_state')));
		}

		return $this;

	}

	/**
	 * Get the path to this category
	 *
	 * @param array $path The starting path
	 *
	 * @return array The calculated path
	 *
	 * @since 2.0
	 */
	public function getPath($path = array()) {

		$path[] = $this->id;

		if ($this->_parent != null) {
			$path = $this->_parent->getPath($path);
		}

		return $path;
	}

	/**
	 * Get the items in this category
	 *
	 * @param boolean $published Fetch only the published items. Default: false
	 * @param JUser $user The User object
	 * @param string $orderby The order by string to apply
	 *
	 * @return array The list of items fetched
	 *
	 * @since 2.0
	 */
	public function getItems($published = false, $user = null, $orderby = '') {
		if (empty($this->_items)) {
			if (!empty($this->item_ids)) {
				$this->_items = $this->app->table->item->getByIds($this->item_ids, $published, $user, $orderby);
			} else {
				$this->_items = $this->app->table->item->getByCategory($this->application_id, $this->id, $published, $user, $orderby);
			}
		}

		return $this->_items;
	}

	/**
	 * Get the item count in this category
	 *
	 * @return int The total number of items in this category
	 *
	 * @since 2.0
	 */
	public function itemCount() {
		if (!isset($this->_item_count)) {
			$this->_item_count = count($this->item_ids);
		}
		return $this->_item_count;
	}

	/**
	 * Get the total item count, including the items in the subcategories
	 *
	 * @return int The total number of items, subcategories items included
	 *
	 * @since 2.0
	 */
	public function totalItemCount() {
		if (!isset($this->_total_item_count)) {
			$this->_total_item_count = count($this->getItemIds(true));
		}

		return $this->_total_item_count;
	}

	/**
	 * Get the items count in this category and in the subcategories
	 *
	 * @return int The total number of items
	 *
	 * @deprecated 2.5 Use Category::totalItemCount() instead
	 *
	 * @since 2.0
	 */
	public function countItems() {
		return $this->totalItemCount();
	}

	/**
	 * Check the total number of items in the subcategories
	 *
	 * @return int The total number of items in the subcategories
	 *
	 * @deprecated 2.5 Use Category::childrenHaveItems() instead
	 *
	 * @since 2.0
	 */
	public function countChildrensItems() {
		return $this->childrenHaveItems();
	}

	/**
	 * Get the list of items ids for this category
	 *
	 * @param boolean $recursive Get the ids also from the subcategories. Default: false
	 *
	 * @return array The list of item ids
	 *
	 * @since 2.0
	 */
	public function getItemIds($recursive = false) {
		$item_ids = $this->item_ids;
		if ($recursive) {
			foreach ($this->getChildren(true) as $child) {
				$item_ids += $child->item_ids;
			}
		}

		return $item_ids;
	}

	/**
	 * Check if the children categories have items
	 *
	 * @return boolean If the children have items
	 *
	 * @since 2.0
	 */
	public function childrenHaveItems() {
		foreach ($this->getChildren(true) as $child) {
			if ($child->itemCount()) {
				return true;
			}
		}

		return false;
	}

    /**
     * Get the parameters for the category
     *
     * @param string $for The scope to get the parameters for ('site' or all). Default: all
     *
     * @return ParameterData The parameters
     *
     * @since 2.0
     */
	public function getParams($for = null) {

		// get site params and inherit globals
		if ($for == 'site') {

			return $this->app->parameter->create()
				->set('config.', $this->getApplication()->getParams()->get('global.config.'))
				->set('template.', $this->getApplication()->getParams()->get('global.template.'))
				->loadArray($this->params);
		}

		return $this->params;
	}

    /**
     * Get the image resource informations as an array
     *
     * @param string $name The image parameter name
     *
     * @return array The image informations
     *
     * @since 2.0
     */
	public function getImage($name) {
		if ($image = $this->params->get($name)) {
			return $this->app->html->_('zoo.image', $image, $this->params->get($name . '_width'), $this->params->get($name . '_height'));
		}
		return null;
	}

	/**
	 * Trigger the content plugins on a given text
	 *
	 * @param string $text The text to trigger the plugins on
	 *
	 * @return string The processed text
	 *
	 * @since 2.0
	 */
	public function getText($text) {
		return $this->app->zoo->triggerContentPlugins($text, array(), 'com_zoo.category.description');
	}

}

/**
 * Exception for the Category class
 *
 * @see Category
 */
class CategoryException extends AppException {}