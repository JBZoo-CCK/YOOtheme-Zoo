<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * App framework class.
 *
 * This class acts as the generic dispatcher and provides
 * also access to every helper available through the use
 * of the magic method __get
 *
 * @package Framework.Classes
 * @since 1.0.0
 */
class App {

	/**
	 * The framework version
	 *
	 * @var string
	 * @since 1.0.0
	 */
    const VERSION = '1.0.0';

	/**
	 * Unique identifier for this App
	 *
	 * @var string
	 * @since 1.0.0
	 */
    public $id;

	/**
	 * The list of helpers currently loaded
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $_helpers = array();

	/**
	 * Static container for the applications instances
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected static $_instances = array();

	/**
	 * Class constructor
	 *
	 * @param string $id The unique identifier of the application
	 * @since 1.0.0
	 */
	public function __construct($id) {

		// init vars
		$this->id = $id;

		// set defaults
		$path = dirname(dirname(__FILE__));
		$this->addHelper(new PathHelper($this));
		$this->addHelper(new UserAppHelper($this));
		$this->path->register(JPATH_ROOT, 'root');
		$this->path->register(JPATH_ROOT.'/media', 'media');
		$this->path->register($path.'/classes', 'classes');
		$this->path->register($path.'/data', 'data');
		$this->path->register($path.'/helpers', 'helpers');
		$this->path->register($path.'/loggers', 'loggers');

	}

	/**
	 * Gets an instance of an application
	 *
	 * @param string $id The application unique identifier
	 *
	 * @return App A App Object
	 *
	 * @since 1.0.0
	 */
	public static function getInstance($id) {

		// add instance, if not exists
		if (!isset(self::$_instances[$id])) {
			self::$_instances[$id] = new App($id);
		}

		return self::$_instances[$id];
	}

	/**
	 * Retrives an helper file
	 *
	 * @param string $name The name of the helper to retrieve
	 *
	 * @return AppHelper The helper instance requested
	 *
	 * @since 1.0.0
	 */
	public function getHelper($name) {

		// try to load helper, if not found
		if (!isset($this->_helpers[$name])) {
		    $this->loadHelper($name);
		}

		// get helper
		if (isset($this->_helpers[$name])) {
			return $this->_helpers[$name];
		}

		return null;
	}

	/**
	 * Adds a helper
     *
	 * @param AppHelper $helper Helper object
	 * @param string $alias Optional Helper alias
	 *
	 * @since 1.0.0
	*/
	public function addHelper($helper, $alias = null) {

		// add to helpers
		$name = $helper->getName();
		$this->_helpers[$name] = $helper;

		// add alias
		if (!empty($alias)) {
			$this->_helpers[$alias] = $helper;
		}

	}

	/**
	 * Load an helper from the path
	 *
	 * @param string|array $helpers Helper name or list of helper names to load
	 * @param string $suffix The suffix of the helper class. Default is "Helper"
	 *
	 * @since 1.0.0
	 */
	public function loadHelper($helpers, $suffix = 'Helper') {
		$helpers = (array) $helpers;

		foreach ($helpers as $name) {
			$class = $name.$suffix;

			// autoload helper class
			if (!class_exists($class) && ($file = $this->path->path('helpers:'.$name.'.php'))) {
			    require_once($file);
			}

			// add helper, if not exists
			if (!isset($this->_helpers[$name])) {
				$this->addHelper(new $class($this));
			}
		}
	}

	/**
	 * Retrieve an Helper
	 *
	 * @param string $name The helper name
	 *
	 * @return AppHelper The helper instance requested
	 *
	 * @since 1.0.0
	 */
	public function __get($name) {
		return $this->getHelper($name);
	}

	/**
	 * Get link to this component's related resources.
	 *
	 * @param array $query The query parameters
	 * @param boolean $xhtml If the link should be valid xhtml
	 * @param boolean $ssl If the link should be forced to be ssl
	 *
	 * @return string The url requested
	 *
	 * @since 1.0.0
	 */
	public function link($query = array(), $xhtml = true, $ssl = null) {
		return $this->component->{$this->id}->link($query, $xhtml, $ssl);
	}

	/**
	 * Get a configuration property of this component.
	 *
	 * @param string $property The name of the property
	 * @param mixed $default The default value. Default is null
	 *
	 * @return mixed The property value
	 *
	 * @since 1.0.0
	 */
	public function get($property, $default = null) {
		return $this->component->{$this->id}->get($property, $default);
	}

	/**
	 * Set a configuration property of this component.
	 *
	 * @param string $property The name of the property
	 * @param mixed $value The value of the property
	 *
	 * @return mixed The previous value of the property
	 *
	 * @since 1.0.0
	 */
	public function set($property, $value = null) {
		return $this->component->{$this->id}->set($property, $value);
	}

	/**
	 * Dispatch the app controller
	 *
	 * @param string $default The default controller name
	 * @param array $config Additional config options
	 *
	 * @since 1.0.0
	 */
	public function dispatch($default = null, $config = array()) {

		// init vars
		$controller = $this->request->get('controller', 'word');
		$task       = $this->request->get('task', 'cmd');

		// load controller
		if ($file = $this->path->path('controllers:'.$controller.'.php')) {
			require_once($file);
		} elseif ($default != null) {
			$controller = $default;
			if ($file = $this->path->path('controllers:'.$controller.'.php')) {
				require_once($file);
			}
		}

		// controller loaded ?
		$class = $controller.'Controller';
		if (class_exists($class)) {

			// perform the request task
			$ctrl = new $class($this, $config);
			$ctrl->execute($task);
			$ctrl->redirect();

		} else {
			throw new AppException("Controller class not found. ($class)");
		}

	}

}

/**
 * AppException identifies an Exception in the App class
 * @see App
 */
class AppException extends Exception {

	/**
	 * Converts the exception to a human readable string
	 *
	 * @return string The error message
	 *
	 * @since 1.0.0
	 */
	public function __toString() {
		return $this->getMessage();
	}

}