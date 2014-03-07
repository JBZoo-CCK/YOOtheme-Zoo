<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class for reading and writing in various formats
 *
 * @package Framework.Classes
 */
class AppData extends ArrayObject {

	/**
	 * Class constructor
	 *
	 * @param array $data The data array
	 */
	public function __construct($data = array()) {
		parent::__construct($data ? $data : array());
	}

	/**
	 * Checks if the given key is present
	 *
	 * @param string $name The key to check
	 *
	 * @return boolean If the key was found
	 *
	 * @since 1.0.0
	 */
	public function has($name) {
		return $this->offsetExists($name);
	}

	/**
	 * Get a value from the data given its key
	 *
	 * @param string $key The key used to fetch the data
	 * @param mixed $default The default value
	 *
	 * @return mixed The fetched value
	 *
	 * @since 1.0.0
	 */
	public function get($key, $default = null) {

		if ($this->offsetExists($key)) {
			return $this->offsetGet($key);
		}

		return $default;
	}

	/**
	 * Set a value in the data
	 *
	 * @param string $name The key used to set the value
	 * @param mixed $value The value to set
	 *
	 * @return AppData The AppData object itself to allow chaining
	 *
	 * @since 1.0.0
	 */
	public function set($name, $value) {
		$this->offsetSet($name, $value);
		return $this;
	}

	/**
	 * Remove a value from the data
	 *
	 * @param string $name The key of the data to remove
	 *
	 * @return AppData The AppData object itself to allow chaining
	 *
	 * @since 1.0.0
	 */
	public function remove($name) {
		$this->offsetUnset($name);
		return $this;
	}

	/**
	 * Magic method to allow for correct isset() calls
	 *
	 * @param string $name The key to search for
	 *
	 * @return boolean If the value was found
	 *
	 * @since 1.0.0
	 */
	public function __isset($name) {
		return $this->offsetExists($name);
	}

	/**
	 * Magic method to get values as object properties
	 *
	 * @param string $name The key of the data to fetch
	 *
	 * @return mixed The value for the given key
	 *
	 * @since 1.0.0
	 */
	public function __get($name) {
		return $this->offsetGet($name);
	}

 	/**
	 * Magic method to set values through object properties
	 *
	 * @param string $name The key of the data to set
	 * @param mixed $value The value to set
	 *
	 * @since 1.0.0
	 */
	public function __set($name, $value) {
		$this->offsetSet($name, $value);
	}

 	/**
	 * Magic method to unset values using unset()
	 *
	 * @param string $name The key of the data to set
	 *
	 * @since 1.0.0
	 */
	public function __unset($name) {
		$this->offsetUnset($name);
	}

 	/**
	 * Magic method to convert the data to a string
	 *
	 * Returns a serialized version of the data contained in
	 * the data object using serialize()
	 *
	 * @return string A serialized version of the data
	 *
	 * @since 1.0.0
	 */
    public function __toString() {
        return empty($this) ? '' : $this->_write($this->getArrayCopy());
    }

	/**
	 * Utility Method to serialize the given data
	 *
	 * @param mixed $data The data to serialize
	 *
	 * @return string The serialized data
	 *
	 * @since 1.0.0
	 */
	protected function _write($data) {
		return serialize($data);
	}

	/**
	 * Find a key in the data recursively
	 *
	 * This method finds the given key, searching also in any array or
	 * object that's nested under the current data object.
	 *
	 * Example:
	 * <code>
	 * $data->find('parentkey.subkey');
	 * </code>
	 *
	 * @param string $key The key to search for. Can be composed using $separator as the key/subkey separator
	 * @param mixed $default The default value
	 * @param string $separator The separator to use when searching for subkeys. Default is '.'
	 *
	 * @return mixed The searched value
	 *
	 * @since 1.0.0
	 */
	public function find($key, $default = null, $separator = '.') {

		$key   = (string) $key;
		$value = $this->get($key);

		// check if key exists in array
		if ($value !== null) {
			return $value;
		}

		// explode search key and init search data
		$parts = explode($separator, $key);
		$data  = $this;

		foreach ($parts as $part) {

			// handle ArrayObject and Array
			if (($data instanceof ArrayObject || is_array($data)) && isset($data[$part])) {

                if ($data[$part] === null) {
                    return $default;
                }

				$data =& $data[$part];
				continue;
			}

			// handle object
			if (is_object($data) && isset($data->$part)) {

                if ($data->$part === null) {
                    return $default;
                }

				$data =& $data->$part;
				continue;
			}

			return $default;
		}

		// return existing value
		return $data;
	}

	/**
	 * Find a value also in nested arrays/objects
	 *
	 * @param mixed $needle The value to search for
	 *
	 * @return string The key of that value
	 *
	 * @since 1.0.0
	 */
	public function searchRecursive($needle) {
		$aIt = new RecursiveArrayIterator($this);
		$it	 = new RecursiveIteratorIterator($aIt);

		while ($it->valid()) {
			if ($it->current() == $needle) {
				return $aIt->key();
			}

			$it->next();
		}

		return false;
	}

	/**
	 * Return flattened array copy. Keys are <b>NOT</b> preserved.
	 *
	 * @return array The flattened array copy
	 *
	 * @since 1.0.0
	 */
	public function flattenRecursive() {
		$flat = array();
		foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($this)) as $value) {
			$flat[] = $value;
		}
		return $flat;
	}

}