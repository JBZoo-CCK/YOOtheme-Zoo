<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper for the database table classes
 * 
 * @package Framework.Helpers
 */
class TableHelper extends AppHelper {

	/**
	 * The table prefix
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $_prefix;

	/**
	 * The list of loaded table classes
	 * 
	 * @var array
	 * @since 1.0.0
	 */
	protected $_tables = array();
    
	/**
	 * Class Constructor
	 * 
	 * @param App $app A reference to the global App object
	 */
	public function __construct($app) {
		parent::__construct($app);

		// set table prefix
		$this->_prefix = '#__'.$this->app->id.'_';

		// load class
		$this->app->loader->register('AppTable', 'classes:table.php');
	}

	/**
	 * Get a table object
	 * 
	 * @param string $name The name of the table to retrieve
	 * @param string $prefix An alternative prefix
	 * 
	 * @return AppTable The table object
	 * 
	 * @since 1.0.0
	 */
	public function get($name, $prefix = null) {
		
		// load table class
		$class = $name.'Table';
		$this->app->loader->register($class, 'tables:'.strtolower($name).'.php');

		// set tables prefix
		if ($prefix == null) {
			$prefix = $this->_prefix;
		}
		
		// add table, if not exists
		if (!isset($this->_tables[$name])) {
			$this->_tables[$name] = class_exists($class) ? new $class($this->app) : new AppTable($this->app, $prefix.$name);
		}

		return $this->_tables[$name];
	}
	
	/**
	 * Magic method to get a table using the name
	 * 
	 * @param string $name The name of the table
	 * 
	 * @return AppTable The table object
	 * 
	 * @since 1.0.0
	 */
	public function __get($name) {
		return $this->get($name);
	}
	
}