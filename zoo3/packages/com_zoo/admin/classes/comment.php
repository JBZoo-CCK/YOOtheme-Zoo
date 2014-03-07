<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class to deal with comments
 *
 * @package Component.Classes
 */
class Comment {

	/**
	 * Constants for different states of the comments
	 */
	const STATE_UNAPPROVED = 0;
	const STATE_APPROVED = 1;
	const STATE_SPAM = 2;

	/**
	 * Id of the comment
	 *
	 * @var int
	 * @since 2.0
	 */
	public $id;

	/**
	 * The id of the parent comment
	 *
	 * @var int
	 * @since 2.0
	 */
	public $parent_id;

    /**
     * The id of the item the comment refers to
     *
     * @var int
     * @since 2.0
     */
	public $item_id;

    /**
     * Id of the User who made the comment
     *
     * @var int
     * @since 2.0
     */
	public $user_id;

    /**
     * The type of the user who made the comment
     *
     * @var string
     * @since 2.0
     */
	public $user_type;

	/**
	 * The name of the author of the comment
	 *
	 * @var string
	 * @since 2.0
	 */
	public $author;

	/**
	 * The email of the author of the comment
	 *
	 * @var string
	 * @since 2.0
	 */
	public $email;

	/**
	 * The url of the author of the comment
	 *
	 * @var string
	 * @since 2.0
	 */
	public $url;

	/**
	 * The ip of the author of the comment
	 *
	 * @var string
	 * @since 2.0
	 */
	public $ip;

	/**
	 * The creation date of the comment in mysql DATETIME format
	 *
	 * @var string
	 * @since 2.0
	 */
	public $created;

	/**
	 * The text content of the comment
	 *
	 * @var string
	 * @since 2.0
	 */
	public $content;

    /**
     * The state of the comment
     *
     * @var int
     * @since 2.0
     */
	public $state = 0;

    /**
     * A reference to the global App object
     *
     * @var App
     * @since 2.0
     */
	public $app;

   	/**
   	 * The parent comment
   	 *
   	 * @var Comment
   	 * @since 2.0
   	 */
	protected $_parent;

    /**
     * The comment children list
     *
     * @var array
     * @since 2.0
     */
	protected $_children = array();

	/**
	 * Get the item which the comment refers to
	 *
	 * @return Item The item to which the comments refers
	 *
	 * @since 2.0
	 */
	public function getItem() {
		return $this->app->table->item->get($this->item_id);
	}

	/**
	 * Get the author of the comment
	 *
	 * @return CommentAuthor The author of the comment
	 *
	 * @since 2.0
	 */
	public function getAuthor() {

		static $authors = array();

		$key = md5($this->author . $this->email . $this->url . $this->user_id);
		if (!isset($authors[$key])) {
			$item = $this->getItem();
			$application = $item ? $item->getApplication() : null;
			$authors[$key] = $this->app->commentauthor->create($this->user_type, array($this->author, $this->email, $this->url, $this->user_id, $application));
		}

		return $authors[$key];

	}

	/**
	 * Set the author of the object
	 *
	 * @param  CommentAuthor $author The author object
	 *
	 * @since 2.0
	 */
	public function bindAuthor(CommentAuthor $author) {
		$this->author = $author->name;
		$this->email = $author->email;
		$this->url = $author->url;

		// set params
		if (!$author->isGuest()) {
			$this->user_id = $author->user_id;
			$this->user_type = $author->getUserType();
		}
	}

	/**
	 * Get the parent comment
	 *
	 * @return Comment The parent comment
	 *
	 * @since 2.0
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * Set the parent comment
	 *
	 * @param Comment $parent The parent comment
	 *
	 * @since 2.0
	 */
	public function setParent($parent) {
		$this->_parent = $parent;
		return $this;
	}

	/**
	 * Get the children comments
	 *
	 * @return array The list of children comments
	 *
	 * @since 2.0
	 */
	public function getChildren() {
		return $this->_children;
	}

	/**
	 * Add a child to the comment
	 *
	 * @param Comment $child The child comment to add
	 *
	 * @return Comment $this for chaining support
	 *
	 * @since 2.0
	 */
	public function addChild($child) {
		$this->_children[$child->id] = $child;
		return $this;
	}

	/**
	 * Remove a child comment from the children list
	 *
	 * @param  Comment $child The child to remove
	 *
	 * @return Comment        $this for chaining support
	 *
	 * @since 2.0
	 */
	public function removeChild($child) {
		unset($this->_children[$child->id]);
		return $this;
	}

	/**
	 * Check if the comment has children comments
	 *
	 * @return boolean If the comment has children
	 *
	 * @since 2.0
	 */
	public function hasChildren() {
		return !empty($this->_children);
	}

	/**
	 * Get the comment pathway
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
	 * Set the comment state and fires the comment:stateChanged event
	 *
	 * @param int  $state The new state of the comment
	 * @param boolean $save  If the change should be saved to the database (default: false)
	 */
	public function setState($state, $save = false) {

		if ($this->state != $state) {

			// set state
			$old_state   = $this->state;
			$this->state = $state;

			// autosave comment ?
			if ($save) {
				$this->app->table->comment->save($this);
			}

			// fire event
		    $this->app->event->dispatcher->notify($this->app->event->create($this, 'comment:stateChanged', compact('old_state')));
		}

		return $this;
	}

}