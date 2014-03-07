<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Table class to load records from the database
 *
 * @package Framework.Classes
 */
class AppTable {

	/**
	 * A reference to the global App object
	 *
	 * @var App
	 * @since 1.0.0
	 */
	public $app;

	/**
	 * The table name
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $name;

	/**
	 * The name of the table's primary key
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $key;

	/**
	 * The name of the class representing the table
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $class;

	/**
	 * The list of fields of the table
	 *
	 * @var array
	 * @since 1.0.0
	 */
	public $fields;

	/**
	 * A reference to the database helper
	 *
	 * @var DatabaseHelper
	 * @since 1.0.0
	 */
	public $database;

	/**
	 * A list of the objects created from the records fetched from the database
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $_objects = array();

	/**
	 * Class Constructor
	 *
	 * @param App $app The global App object
	 * @param string $name The name of the table
	 * @param string $key The primary key of the table (default: id)
	 */
	public function __construct($app, $name, $key = 'id') {

		// init vars
		$this->app = $app;
		$this->name = $name;
		$this->key = $key;
		$this->database = $app->database;

		// set default class name
		$this->class = get_class($this) == __CLASS__ ? 'stdClass' : basename(get_class($this), 'Table');

		// load class
		$this->app->loader->register($this->class, 'classes:'.strtolower($this->class).'.php');
	}

	/**
	 * Get the list of columns for the table
	 *
	 * @return array The list of columns
	 *
	 * @since 1.0.0
	 */
	public function getTableColumns() {

		if (empty($this->fields)) {
			$this->fields = $this->database->getTableColumns($this->name);
		}

		return $this->fields;
	}

	/**
	 * Fetch a record from the table
	 *
	 * After fetching the record, it also creates an object representing the record
	 * and inits it with the basic informations
	 *
	 * @param mixed $key The id of the record to fetch
	 * @param boolean $new If we should fetch the object fresh from the database instead of the cached one
	 *
	 * @return mixed The object representing the table record
	 *
	 * @since 1.0.0
	 */
	public function get($key, $new = false) {

		if ($key === '' || $key === null) return false;

		$options = array('conditions' => array($this->key.' = ?', $key));

		// get new object
		if ($new) {
			return $this->find('first', $options);
		}

		// get saved object instance
		if (!isset($this->_objects[$key])) {
			$this->_objects[$key] = $this->find('first', $options);
		}

		return $this->_objects[$key];
	}

	/**
	 * Get the first result of the query built using the options passed
	 *
	 * @see _select()
	 *
	 * @param array $options The list of conditions for the query
	 *
	 * @return mixed The object representing the table record
	 */
	public function first($options = null) {
		return $this->find('first', $options);
	}

	/**
	 * Get all the results of the query built using the options passed
	 *
	 * @see _select()
	 *
	 * @param array $options The list of conditions for the query
	 *
	 * @return array The list of objects representing the table record
	 */
	public function all($options = null) {
		return $this->find('all', $options);
	}

	/**
	 * Get the results of the query built using the options passed
	 *
	 * This is the general method used to query the table.
	 * The mode parameter will be used to fetch the objects in the various ways
	 *
	 * @see _select()
	 *
	 * @param string $mode The mode with which the items will be fetched (all, first)
	 * @param array $options The list of conditions for the query
	 *
	 * @return array|mixed The object representing the table record or the list of objects, depending on the mode
	 *
	 * @since 1.0.0
	 */
	public function find($mode = 'all', $options = null) {

		$options = is_array($options) ? $options : array();
		$query   = $this->_select($options);

		if ($mode == 'first') {
			return $this->_queryObject($query);
		}

		return $this->_queryObjectList($query);
	}

	/**
	 * Get the number of rows returned by the query generated using the given options
	 *
	 * @see _select()
	 *
	 * @param array $options The list of conditions for the query
	 *
	 * @return int The number of rows
	 *
	 * @since 1.0.0
	 */
	public function count($options = null) {

		$options = is_array($options) ? $options : array();
		$query   = $this->_select($options);

		$this->database->query($query);

		return $this->database->getNumRows();
	}

	/**
	 * Save a record in table using the given data
	 *
	 * This method should always receive an object, which is usually the object representing
	 * the table record populated with the changed data. If it's a new item (id=0) the system
	 * will do an insert, otherwise it will perform an update
	 *
	 * @param object $object The object containing the data to be stored
	 *
	 * @since 1.0.0
	 */
	public function save($object) {

		// init vars
		$vars   = get_object_vars($object);
		$fields = $this->getTableColumns();

		foreach ($fields as $key => $value) {
			$fields[$key] = array_key_exists($key, $vars) ? (string) $vars[$key] : null;
		}

		// insert or update database
		$obj = (object) $fields;
		$key = $this->key;
		if ($obj->$key) {

			// update object
			$this->database->updateObject($this->name, $obj, $key);

		} else {

			// insert object
			$this->database->insertObject($this->name, $obj, $key);

			// set insert id
			$object->$key = $obj->$key;
		}

	}

	/**
	 * Deletes a record from the table
	 *
	 * The methods expects the object representing the record to be deleted.
	 * It will use the primary key value found in that object to perform the delete operation
	 *
	 * @param object $object The object representing the record to be deleted
	 *
	 * @return boolean If the operation was successful
	 *
	 * @since 1.0.0
	 */
	public function delete($object) {

		// get table key
		$key = $this->key;

		// delete object
		$query = 'DELETE FROM '.$this->name.
				 ' WHERE '.$key.' = '.$this->database->escape($object->$key);

		return $this->_query($query);
	}

	/**
	 * Removes the object from the internal object storage.
	 *
	 * @param mixed $key The key of the object to be removed
	 *
	 * @since 1.0.0
	 */
	public function unsetObject($key) {
		if (isset($this->_objects[$key])) {
			unset($this->_objects[$key]);
		}
	}

	/**
	 * Checks if the object is already managed by the table.
	 *
	 * @param mixed $key The key of the object
	 *
	 * @since 2.6.5
	 */
	public function has($key) {
		return isset($this->_objects[$key]);
	}

	/**
	 * Builds the query using the given options
	 *
	 * @param array $options The list of options for the query (select, from, conditions, group, order, limit, offset)
	 *
	 * @return string The query
	 *
	 * @since 1.0.0
	 */
	protected function _select(array $options) {

		// select
		$query[] = sprintf('SELECT %s', isset($options['select']) ? $options['select'] : '*');

		// from
		$query[] = sprintf('FROM %s', isset($options['from']) ? $options['from'] : $this->name);

		// where
		if (isset($options['conditions'])) {
			$condition  = '';
			$conditions = (array) $options['conditions'];

			// parse condition
			$parts = explode('?', array_shift($conditions));
			foreach ($parts as $part) {
				$condition .= $part.$this->database->escape(array_shift($conditions));
			}

			if (!empty($condition)) {
				$query[] = sprintf('WHERE %s', $condition);
			}
		}

		// group by
		if (isset($options['group'])) {
			$query[] = sprintf('GROUP BY %s', $options['group']);
		}

		// order
		if (isset($options['order'])) {
			$query[] = sprintf('ORDER BY %s', $options['order']);
		}

		// offset & limit
		if (isset($options['offset']) || isset($options['limit'])) {
			$offset  = isset($options['offset']) ? (int) $options['offset'] : 0;
			$limit   = isset($options['limit']) ? (int) $options['limit'] : 0;
			$query[] = sprintf('LIMIT %s, %s', $offset, $limit);
		}

		return implode(' ', $query);
	}

	/**
	 * Performs a query to the database
	 *
	 * @param string $query The query to perform
	 *
	 * @return boolean The result of the query operation
	 *
	 * @since 1.0.0
	 */
	protected function _query($query) {
		return $this->database->query($query);
	}

	/**
	 * Performs a query to the database and returns the result
	 *
	 * @param string $query The query to perform
	 *
	 * @return mixed The result of the query
	 *
	 * @since 1.0.0
	 */
	protected function _queryResult($query) {
		return $this->database->queryResult($query);
	}

	/**
	 * Performs a query to the database and returns the representing object
	 *
	 * This method will run the query and then build the object that represents
	 * the record of the table. It will also init the object with the basic properties
	 * like the reference to the global App object
	 *
	 * @param string $query The query to perform
	 *
	 * @return object The object representing the record
	 */
	protected function _queryObject($query) {

		// query database
		$result = $this->database->query($query);

		// fetch object and execute init callback
		$object = null;
		if ($object = $this->database->fetchObject($result, $this->class)) {
			$object = $this->_initObject($object);
		}

		$this->database->freeResult($result);
		return $object;
	}

	/**
	 * Performs a query to the database and returns the representing list of objects
	 *
	 * This method will run the query and then build the list of objects that represent
	 * the records of the table. It will also init the objects with the basic properties
	 * like the reference to the global App object
	 *
	 * @param string $query The query to perform
	 *
	 * @return array The list of objects representing the records
	 */
	protected function _queryObjectList($query) {

		// query database
		$result = $this->database->query($query);

		// fetch objects and execute init callback
		$objects = array();
		while ($object = $this->database->fetchObject($result, $this->class)) {
			$objects[$object->{$this->key}] = $this->_initObject($object);
		}

		$this->database->freeResult($result);
		return $objects;
	}

	/**
	 * Init the object adding the reference to the global app object
	 *
	 * @param object $object The object to init
	 *
	 * @return object The object with an app property referencing the gobal app object
	 */
	protected function _initObject($object) {

		// add reference to related app instance
		if (property_exists($object, 'app')) {
			$object->app = $this->app;
		}

		return $object;
	}

}

/**
 * Dedicated exception for the AppTable class
 *
 * @see AppTable
 */
class AppTableException extends AppException {}