<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper to manage error. Wrapper for JError
 * 
 * @package Framework.Helpers
 */
class ErrorHelper extends AppHelper {

	/**
	 * Map all the methods of JError to the helper
	 * 
	 * @param string $method The method name
	 * @param array $args The list of arguments to pass on to the method
	 * 
	 * @return mixed The result of the called method
	 * 
	 * @since 1.0.0
	 */
    public function __call($method, $args) {
		return $this->_call(array('JError', $method), $args);
    }
	
}