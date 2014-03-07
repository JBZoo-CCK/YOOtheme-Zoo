<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper to access Component Configuration
 * 
 * @package Framework.Helpers
 */
class ComponentHelper extends AppHelper {

	/**
	 * List of components
	 * 
	 * @var array
	 * @static
	 * @since 1.0.0
	 */
	protected static $_components = array();

	/**
	 * Class Constructor
	 * 
	 * @param App $app A reference to the global App object
	 */
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppComponent', 'classes:component.php');
	}

	/**
	 * Get a component
	 * 
	 * @param string $name The name of the component to retrieve
	 * 
	 * @return AppComponent The component object
	 * 
	 * @since 1.0.0
	 */
	public function __get($name) {

		// get component name
		if ($name == 'self') {
			$name = 'com_'.$this->app->id;
		} elseif ($name == 'active') {
			$name = $this->app->system->application->scope;
		} elseif (strpos($name, 'com_') === false) {
			$name = 'com_'.$name;
		}

		// add component, if not exists
		if (!isset(self::$_components[$name])) {
			self::$_components[$name] = new AppComponent($this->app, $name);
		}

		return self::$_components[$name];
	}

}