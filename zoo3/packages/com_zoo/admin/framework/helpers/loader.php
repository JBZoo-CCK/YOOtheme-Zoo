<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper for the loading of classes. Wrapper for JLoader
 * 
 * @package Framework.Helpers
 */
class LoaderHelper extends AppHelper {

	/**
	 * Register a class with the loader
	 * 
	 * @param string $class the class name to load
	 * @param string $file The file that contains the class
	 * 
	 * @since 1.0.0
	 */
	public function register($class, $file) {
		if (!class_exists($class)) {
			JLoader::register($class, $this->app->path->path($file));
		}
	}

	/**
	 * Map all the methods to the JLoader class
	 * 
	 * @param string $method The method name
	 * @param array $args The list of arguments
	 * 
	 * @return mixed The method result
	 * 
	 * @since 1.0.0
	 */
    public function __call($method, $args) {
		return $this->_call(array('JLoader', $method), $args);
    }

}