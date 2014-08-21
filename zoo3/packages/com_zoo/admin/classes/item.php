<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class representing an item
 *
 * @package Component.Classes
 */
class Item {

    /**
     * The id of the item
     *
     * @var int
     * @since 2.0
     */
	public $id;

    /**
     * The id of the application the item belongs to
     *
     * @var int
     * @since 2.0
     */
	public $application_id;

    /**
     * The type identifier of the Item
     *
     * @var string
     * @since 2.0
     */
	public $type;

    /**
     * The name of the item
     *
     * @var string
     * @since 2.0
     */
	public $name;

    /**
     * The alias of the item
     *
     * @var string
     * @since 2.0
     */
	public $alias;

    /**
     * The creation date of the item in mysql DATETIME format
     *
     * @var string
     * @since 2.0
     */
	public $created;

    /**
     * The last modified date of the item in mysql DATETIME format
     *
     * @var string
     * @since 2.0
     */
	public $modified;

    /**
     * The id of the user that last modified the item
     *
     * @var int
     * @since 2.0
     */
	public $modified_by;

    /**
     * The date from which the item should be published
     *
     * @var string
     * @since 2.0
     */
	public $publish_up;

    /**
     * The date up until the item should be published
     *
     * @var string
     * @since 2.0
     */
	public $publish_down;

   	/**
   	 * The item priority. An higher priority means that an item should be shown before
   	 *
   	 * @var int
   	 * @since 2.0
   	 */
	public $priority = 0;

    /**
     * Hits count for the item
     *
     * @var int
     * @since 2.0
     */
	public $hits = 0;

    /**
     * Item published state
     *
     * @var int
     * @since 2.0
     */
	public $state = 0;

    /**
     * If an item should be searchable
     *
     * @var boolean
     * @since 2.0
     */
	public $searchable = 1;

    /**
     * The access level required to see this item
     *
     * @var int
     * @since 2.0
     */
	public $access;

    /**
     * The id of the user that created the item
     *
     * @var int
     * @since 2.0
     */
	public $created_by;

    /**
     * The name of the user that created the item
     *
     * @var string
     * @since 2.0
     */
	public $created_by_alias;

    /**
     * The item parameters
     *
     * @var ParameterData
     * @since 2.0
     */
	public $params;

    /**
     * The elements of the item encoded in json format
     *
     * @var string
     * @since 2.0
     */
	public $elements;

    /**
     * A reference to the global App object
     *
     * @var App
     * @since 2.0
     */
	public $app;

    /**
     * The item type
     *
     * @var Type
     * @since 2.0
     */
	protected $_type;

    /**
     * The list of elements of the item
     *
     * @var array
     * @since 2.0
     */
	protected $_elements;

    /**
     * The list of tags for this item
     *
     * @var array
     * @since 2.0
     */
	protected $_tags;

    /**
     * The primary category for this item
     *
     * @var Category
     * @since 2.0
     */
	protected $_primary_category;

    /**
     * The related categories for this item
     *
     * @var array
     * @since 2.0
     */
	protected $_related_categories;

    /**
     * The ids of the realated categories for this item
     *
     * @var array
     * @since 2.0
     */
	protected $_related_category_ids;

 	/**
 	 * Class Constructor
 	 */
	public function __construct() {

		// get app instance
		$app = App::getInstance('zoo');

		// decorate data as object
		$this->params = $app->parameter->create($this->params);

		// decorate data as object
		$this->elements = $app->data->create($this->elements);

	}

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canEdit($user = null) {
        return $this->getType()->canEdit($user, $this->created_by);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canEditState($user = null) {
        return $this->getType()->canEditState($user);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canCreate($user = null) {
        return $this->getType()->canCreate($user);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canDelete($user = null) {
        return $this->getType()->canDelete($user);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canManageComments($user = null) {
        return $this->getApplication()->canManageComments($user);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canManageFrontpage($user = null) {
        return $this->getApplication()->canManageFrontpage($user);
    }

	/**
	 * Get the Application which the item belongs to
	 *
	 * @return Application The application
	 *
	 * @since 2.0
	 */
	public function getApplication() {
		return $this->app->table->application->get($this->application_id);
	}

	/**
	 * Get the item Type
	 *
	 * @return Type The item Type
	 *
	 * @since 2.0
	 */
	public function getType() {

		if (empty($this->_type)) {
			$this->_type = $this->getApplication()->getType($this->type);
		}

		return $this->_type;
	}

	/**
	 * Get the name of the user that created the item
	 *
	 * @return string The name of the author
	 *
	 * @since 2.0
	 */
	public function getAuthor() {

		$author = $this->created_by_alias;

		if (!$author) {

			$user = $this->app->user->get($this->created_by);

			if ($user && $user->id) {
				$author = $user->name;
			}
		}

		return $author;
	}

	/**
	 * Get the item published state
	 *
	 * @return int The item state
	 *
	 * @since 2.0
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * Set the item published state
	 *
	 * @param int  $state The new item state
	 * @param boolean $save  If the change should be saved to the database
	 *
	 * @return Item $this for chaining support
	 *
	 * @since 2.0
	 */
	public function setState($state, $save = false) {
        // check ACL
        if (!$this->canEditState()) {
            return false;
        }

		if ($this->state != $state) {

			// set state
			$old_state   = $this->state;
			$this->state = $state;

			// autosave comment ?
			if ($save) {
				$this->app->table->item->save($this, false);
			}

			// fire event
		    $this->app->event->dispatcher->notify($this->app->event->create($this, 'item:stateChanged', compact('old_state')));
		}

		return $this;
	}

    /**
     * Returns asset name of the item
     *
     * @return string Asset name
     *
     * @since 3.2
     */
    public function getAssetName() {
        return $this->getType()->getAssetName();
    }

	/**
	 * If an item is searchable
	 *
	 * @return boolean If an item is searchable
	 *
	 * @since 2.0
	 */
	public function getSearchable() {
		return $this->searchable;
	}

	/**
	 * Set if an item should be searchable
	 *
	 * @param boolean $val If the item should be searchable
	 *
	 * @return Item $this for chaining support
	 *
	 * @since 2.0
	 */
	public function setSearchable($val) {
		$this->searchable = $val;
		return $this;
	}

	/**
	 * Get an element object out of this item
	 *
	 * @param  string $identifier The element identifier
	 *
	 * @return Element             The element object
	 *
	 * @since 2.0
	 */
	public function getElement($identifier) {

		if (isset($this->_elements[$identifier])) {
			return $this->_elements[$identifier];
		}

		if ($element = $this->getType()->getElement($identifier)) {
			$element->setItem($this);
			$this->_elements[$identifier] = $element;
			return $element;
		}

		return null;
	}

	/**
	 * Get a list of the Core Elements
	 *
	 * @return array The list of core elements
	 *
	 * @since 2.0
	 */
	public function getCoreElements() {

		// get types core elements
		if ($type = $this->getType()) {
			$core_elements = $type->getCoreElements();
			foreach ($core_elements as $element) {
				$element->setItem($this);
			}
			return $core_elements;
		}

		return array();
	}

	/**
	 * Get the list of elements
	 *
	 * @return array The element list
	 *
	 * @since 2.0
	 */
	public function getElements() {

		// get types elements
		if ($type = $this->getType()) {
			foreach ($type->getElements() as $element) {
				if (!isset($this->_elements[$element->identifier])) {
					$element->setItem($this);
					$this->_elements[$element->identifier] = $element;
				}
			}
			$this->_elements = $this->_elements ? $this->_elements : array();
		}

		return $this->_elements;
	}

	/**
	 * Get a list of elements filtered by type
	 *
	 * @return array The element list
	 *
	 * @since 3.0.6
	 */
    public function getElementsByType($type) {
        return array_filter($this->getElements(), create_function('$element', 'return $element->getElementType() == "'.$type.'";'));
    }

	/**
	 * Get a list of elements that support submissions
	 *
	 * @return array The submittable elements
	 *
	 * @since 2.0
	 */
	public function getSubmittableElements() {
		return	array_filter($this->getElements(), create_function('$element', 'return $element instanceof iSubmittable;'));
	}

	/**
	 * Get the related categories for this item
	 *
	 * @param  boolean $published Fetch only the published categories
	 *
	 * @return array             The list of categories
	 *
	 * @since 2.0
	 */
	public function getRelatedCategories($published = false) {
		if ($this->_related_categories === null) {
			$this->_related_categories = $this->app->table->category->getById($this->getRelatedCategoryIds($published), $published);
		}
		return $this->_related_categories;
	}

	/**
	 * Get the related categories ids
	 *
	 * @param  boolean $published Fetch the ids of the published categories only
	 *
	 * @return array             The list of categories ids
	 *
	 * @since 2.0
	 */
	public function getRelatedCategoryIds($published = false) {
		if ($this->_related_category_ids === null) {
			$this->_related_category_ids = $this->app->category->getItemsRelatedCategoryIds($this->id, $published);
		}
		return $this->_related_category_ids;
	}

	/**
	 * Get the primary category
	 *
	 * @return Category Get the primary category
	 *
	 * @since 2.0
	 */
	public function getPrimaryCategory() {
		if (empty($this->_primary_category)) {
			$table = $this->app->table->category;
			if ($id = $this->getPrimaryCategoryId()) {
				$this->_primary_category = $table->get($id);
			}
		}

		return $this->_primary_category;
	}

	/**
	 * Get the id of the primary category
	 *
	 * @return int The id of the primary category
	 *
	 * @since 2.0
	 */
	public function getPrimaryCategoryId() {
		return (int) $this->getParams()->get('config.primary_category', null);
	}

	/**
	 * Get the parameters for the item
	 *
	 * @param  string $for The scope for the parameters (could be 'site' or all)
	 *
	 * @return ParameterData      The parameters
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
	 * Get the tags
	 *
	 * @return array The list of tags
	 *
	 * @since 2.0
	 */
	public function getTags() {

		if ($this->_tags === null) {
			$this->_tags = $this->app->table->tag->getItemTags($this->id);
		}

		return $this->_tags;
	}

	/**
	 * The the item tags
	 *
	 * @param array $tags The tags
	 *
	 * @return Item $this for chaining support
	 *
	 * @since 2.0
	 */
	public function setTags($tags = array()) {

		$this->_tags = array_filter($tags);

		return $this;
	}

	/**
	 * Check if the given usen can access this item
	 *
	 * @param  JUser $user The user to check
	 *
	 * @return boolean       If the user can access the item
	 *
	 * @since 2.0
	 */
	public function canAccess($user = null) {
		return $this->app->user->canAccess($user, $this->access);
	}

	/**
	 * Raise the hit count for this item saving hit to the database
	 *
	 * @return boolean If the operation was successful
	 *
	 * @since 2.0
	 */
	public function hit() {
		return $this->app->table->item->hit($this);
	}

	/**
	 * Get the list of comments
	 *
	 * @return array The list of comments
	 *
	 * @since 2.0
	 */
	public function getComments() {
		return $this->app->table->comment->getCommentsForItem($this->id, $this->getApplication()->getParams()->get('global.comments.order', 'ASC'), $this->app->comment->activeAuthor());
	}

	/**
	 * Get the comment list as a tree
	 *
	 * @return array The comment tree
	 *
	 * @since 2.0
	 */
	public function getCommentTree() {
		return $this->app->tree->build($this->getComments(), 'Comment', $this->getApplication()->getParams()->get('global.comments.max_depth'), 'parent_id');
	}

	/**
	 * Get the total number of comments
	 *
	 * @param  int $state The state of the comments (Default: 1 => approved)
	 *
	 * @return int         The total number of comments
	 *
	 * @since 2.0
	 */
	public function getCommentsCount($state = 1) {
		return $this->app->table->comment->count(array('select' => 'id', 'conditions' => array('item_id = ? AND state = ?', $this->id, $state)));
	}

	/**
	 * Check if the item is published (including the publish dates)
	 *
	 * @return boolean True if the item is published
	 *
	 * @since 2.0
	 */
	public function isPublished() {

		// get dates
		$now  = $this->app->date->create()->toSQL();
		$null = $this->app->database->getNullDate();

		return $this->state == 1
				&& ($this->publish_up == $null || $this->publish_up <= $now)
				&& ($this->publish_down == $null || $this->publish_down >= $now);
	}

	/**
	 * Check if the comments are enabled for this item and in the global app config
	 *
	 * @return boolean If the comments are enabled
	 *
	 * @since 2.0
	 */
	public function isCommentsEnabled() {
		return $this->getParams()->get('config.enable_comments', 1);
	}

	/**
	 * Set an email address as a subscriber to the item comments
	 *
	 * @param  string $mail The email to subscribe
	 * @param  string $name The name of the owner of the email
	 *
	 * @return Item       $this for chaining support
	 *
	 * @since 2.0
	 */
	public function subscribe($mail, $name = '') {

		$subscribers = (array) $this->getParams()->get('comments.subscribers');
		if (!in_array($mail, array_keys($subscribers))) {
			$subscribers[$mail] = $name;
			$this->getParams()->set('comments.subscribers', $subscribers);
		}

		return $this;
	}

	/**
	 * Unsubscribe the email from the list of subscribers
	 *
	 * @param  string $mail The email to unsubscribe
	 *
	 * @return Item       $this for chaining support
	 *
	 * @since 2.0
	 */
	public function unsubscribe($mail) {

		$subscribers = (array) $this->getParams()->get('comments.subscribers');
		if (key_exists($mail, $subscribers)) {
			unset($subscribers[$mail]);
			$this->getParams()->set('comments.subscribers', $subscribers);
		}

		return $this;
	}

	/**
	 * Get the list of the subscribers for this item
	 *
	 * @return array The list of subscribers
	 *
	 * @since 2.0
	 */
	public function getSubscribers() {
		return (array) $this->getParams()->get('comments.subscribers');
	}

}

/**
 * The Exception for the Item class
 *
 * @see Item
 */
class ItemException extends AppException {}