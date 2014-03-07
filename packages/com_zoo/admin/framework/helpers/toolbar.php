<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper for dealing with the toolbar. Wrapper for JToolBarHelper
 * 
 * @package Framework.Helpers
 * 
 * @see JToolBarHelper
 */
class ToolbarHelper extends AppHelper {

	/**
	 * Map all the methods to the JToolBarHelper class
	 * 
	 * @param string $method The name of the method
	 * @param array $args The list of arguments to pass on to the method
	 * 
	 * @return mixed The result of the call
	 * 
	 * @see JToolBarHelper
	 * 
	 * @since 1.0.0
	 */
    public function __call($method, $args) {
		return $this->_call(array('JToolBarHelper', $method), $args);
    }
	
}