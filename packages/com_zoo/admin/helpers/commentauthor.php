<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The comment author helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class CommentAuthorHelper extends AppHelper {

	/**
	 * Class constructor
	 *
	 * @param string $app App instance.
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('CommentAuthor', 'classes:commentauthor.php');
	}

	/**
	 * Creates an CommentAuthor instance
	 *
	 * @param string $type Type of CommentAuthor to create
	 * @param array $args Additional arguments to pass to the constructor
	 *
	 * @return CommentAuthor
	 *
	 * @since 2.0
	 */
	public function create($type = '', $args = array()) {
		return $this->app->object->create(($type ? 'CommentAuthor'.$type : 'CommentAuthor'), $args);
	}

}