<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper for dealing with pagination
 * 
 * @package Framework.Helpers
 */
class PaginationHelper extends AppHelper {
	  
	/**
	 * Create a JPagination object
	 * 
	 * @param int $total The total number of items
	 * @param int $limitstart The starting number of the pagination
	 * @param int $limit The limit of the current pagination
	 * @param string $name The name of the pagination object
	 * @param string $type The type of the paginator object class
	 * 
	 * @return JPagination The pagination object
	 * 
	 * @since 1.0.0
	 */
	public function create($total, $limitstart, $limit, $name = '', $type = '') {

		if (empty($type)) {

			// load class
			jimport('joomla.html.pagination');

			return new JPagination($total, $limitstart, $limit);

		}

		// load Pagination class
		$class = $type.'Pagination';
		$this->app->loader->register($class, 'classes:pagination.php');
		
		return $this->app->object->create($class, array($name, $total, $limitstart, $limit));
		
	}

}