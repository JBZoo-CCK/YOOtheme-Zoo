<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper for creating objects
 * 
 * @package Framework.Helpers
 */
class ObjectHelper extends AppHelper {
    
	/**
	 * Create an object using a given classname
	 * 
	 * @param string $class The class name
	 * @param array $args A list of arguments to pass on to the constructor
	 * 
	 * @return object The created object
	 * 
	 * @since 1.0.0
	 */
	public function create($class, $args = array()) {

		// load class
		$this->app->loader->register($class, 'classes:'.strtolower($class).'.php');

		// use reflection or new for object creation
		if (count($args) > 0) {
			$reflection = new ReflectionClass($class);
			$object = $reflection->newInstanceArgs($args);
		} else {
			$object = new $class();
		}
		
		// add reference to related app instance
		if (property_exists($object, 'app')) {
			$object->app = $this->app;
		}
		
		return $object;
	}

}