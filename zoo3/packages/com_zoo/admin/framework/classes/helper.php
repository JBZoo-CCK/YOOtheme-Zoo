<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper base class
 *
 * @package Framework.Classes
 */
class AppHelper {

	/**
	 * Reference to the global App object
	 *
	 * @var App
	 * @since 1.0.0
	 */
	public $app;

	/**
	 * The name of the helper
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $_name;

	/**
	 * Class constructor
	 *
	 * @param App $app A reference to an App Object
	 */
	public function __construct($app) {

		// set application
		$this->app = $app;

		// set default name
		$this->_name = strtolower(basename(get_class($this), 'Helper'));

	}

	/**
	 * Get the name of the helper
	 *
	 * @return string The name of the helper
	 *
	 * @since 1.0.0
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Execute a function call
	 *
	 * @param callable $callable a php callable, which can be a array($object, 'method') or a 'method'
	 * @param array $args a list of arguments for the method (max 4)
	 *
	 * @return mixed The result of the function call
	 *
	 * @since 1.0.0
	 */
	protected function _call($callable, $args = array()) {
		return call_user_func_array($callable, $args);
	}

}