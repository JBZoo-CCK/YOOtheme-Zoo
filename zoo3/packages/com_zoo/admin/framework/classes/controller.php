<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (!class_exists('JControllerLegacy', false)) {
    jimport('cms.controller.legacy');
    jimport('legacy.controller.legacy');
}

/**
 * The base Controller Class
 *
 * @package Framework.Classes
 */
class AppController extends JControllerLegacy {

	/**
	 * Reference to the global App class
	 *
	 * @var App
	 * @since 1.0.0
	 */
	public $app;

	/**
	 * Reference to the request Helper
	 *
	 * @var RequestHelper
	 * @since 1.0.0
	 */
	public $request;

	/**
	 * The scope of the current request
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $option;

	/**
	 * The name of the controller
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $controller;

	/**
	 * List of the views currently registered for this controller
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected static $_views = array();

	/**
	 * Class Constructor
	 *
	 * @param App The reference to the global app object
	 * @param array An array of configuration values to pass to the controller
	 *
	 * @since 1.0.0
	 */
	public function __construct($app, $config = array()) {
		parent::__construct($config);

		// init vars
		$this->app        = $app;
		$this->request    = $app->request;
		$this->option     = $app->system->application->scope;
		$this->controller = $this->getName();

	}

	/**
	 * Get a reference to the current view and load it if necessary
	 *
	 * @param string $name The name of the view to load. By default load the current view
	 * @param string $type The view type. Optional.
	 * @param string $prefix The class prefix. Optional.
	 * @param array $config An optional configuration array for the view
	 *
	 * @return AppView The view class requested
	 *
	 * @since 1.0.0
	 */
	public function getView($name = '', $type = '', $prefix = '', $config = array()) {

		// set name
		if (empty($name)) {
			$name = $this->getName();
		}

		// create view
		if (empty(self::$_views[$name])) {
			self::$_views[$name] = new AppView(array_merge(array('name' => $name, 'template_path' => JPATH_COMPONENT. '/views/' . $name . '/tmpl'), $config));
		}

		// automatically pass all public class variables on to view
		foreach (get_object_vars($this) as $var => $value) {
			if (substr($var, 0, 1) != '_') {
				self::$_views[$name]->set($var, $value);
			}
		}

		return self::$_views[$name];
	}

 	/**
	 * Binds a named array/hash to an object
	 *
	 * @param object $object The object to which we'll bind the properties to
	 * @param array|object $data An array or object containing the data to be bound
	 * @param array|string $ignore An array or a space separated list of fields to ignore during the binding
	 *
	 * @since 1.0.0
	 */
	public static function bind($object, $data, $ignore = array()) {

		if (!is_array($data) && !is_object($data)) {
			throw new AppException(__CLASS__.'::bind() failed. Invalid from argument');
		}

		if (is_object($data)) {
			$data = get_object_vars($data);
		}

		if (!is_array($ignore)) {
			$ignore = explode(' ', $ignore);
		}

		foreach (get_object_vars($object) as $k => $v) {

			// ignore protected attributes
			if ('_' == substr($k, 0, 1)) {
				continue;
			}

			// internal attributes of an object are ignored
			if (isset($data[$k]) && !in_array($k, $ignore)) {
				$object->$k = $data[$k];
			}
		}
	}

	/**
	 * Translate a string into the current language
	 *
	 * @param string $string The string to translate
	 * @param boolean $js_safe If the string should be made javascript safe
	 *
	 * @return string The translated string
	 */
	public function l($string, $js_safe = false) {
		return $this->app->language->l($string, $js_safe);
	}

}

/**
 * The exception class dedicated for the controller classes
 *
 * @see AppController
 */
class AppControllerException extends AppException {}