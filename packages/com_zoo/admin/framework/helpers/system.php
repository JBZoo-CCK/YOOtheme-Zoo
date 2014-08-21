<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Joomla System Helper. Provides integration with the underlying Joomla! system
 *
 * @package Framework.Helpers
 */
class SystemHelper extends AppHelper {

	/**
	 * The menu item id
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $itemid;

	/**
	 * Variables that can be fetched through JFactory
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected static $_factory = array('application', 'config', 'language', 'user', 'session', 'document', 'acl', 'template', 'dbo', 'mailer', 'editor');

	/**
	 * Class constructor
	 *
	 * @param App $app A reference to the global App object
	 */
	public function __construct($app) {
		parent::__construct($app);

		// init additional site vars
		if ($this->application->isSite()) {
			$this->itemid = $this->app->request->get('Itemid', 'int', 0);
		}

	}

	/**
	 * Wraps Joomla hash method
	 *
	 * @param string $seed
	 *
	 * @return string Md5 hash
	 *
	 * @since 3.6
	 */
	public function getHash($seed) {
		return JApplication::getHash($seed);
	}

	/**
	 * Get a Joomla environment variable
	 *
	 * @param string $name The name of the variable to retrieve
	 *
	 * @return mixed The variable
	 *
	 * @see SystemHelper::$_factory
	 *
	 * @since 1.0.0
	 */
	public function __get($name) {

		$name = strtolower($name);

		if (in_array($name, self::$_factory)) {
			return call_user_func(array('JFactory', 'get'.$name));
		} elseif ($name == 'dispatcher') {
			return JDispatcher::getInstance();
		}

		return null;
	}

	/**
	 * Map all the methods to the JFactory class
	 *
	 * @param string $method The name of the method
	 * @param array $args The list of arguments to pass on to the method
	 *
	 * @return mixed The result of the call
	 *
	 * @see JFactory
	 *
	 * @since 1.0.0
	 */
    public function __call($method, $args) {
		return $this->_call(array('JFactory', $method), $args);
    }

}