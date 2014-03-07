<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper class to create JSONData object representing parameters
 * 
 * @package Framework.Helpers
 */
class ParameterHelper extends AppHelper {

	/**
	 * Get a ParameterData object
	 * 
	 * @param array $params The list of params to convert
	 * 
	 * @return ParameterData The object representing the params
	 * 
	 * @since 1.0.0
	 */
	public function create($params = array()) {
		$this->app->loader->register('JSONData', 'data:json.php');
		return $this->app->data->create($params, 'parameter');
	}

}