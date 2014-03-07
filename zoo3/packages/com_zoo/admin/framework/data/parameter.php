<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/
/**
 * Class for reading and writing parameters in Joomla JRegistry format 
 * 
 * @package Framework.Data
 */
class ParameterData extends JSONData {

	/**
	 * Class Constructor
	 * 
	 * @param array|object $data The data to be converted to JRegistry format
	 * 
	 * @since 1.0.0
	 */
	public function __construct($data = array()) {

		if ($data instanceof JRegistry) {
			$data = $data->toArray();
		} else if (is_string($data) && (substr($data, 0, 1) != '{') && (substr($data, -1, 1) != '}')) {
			$data = JRegistryFormat::getInstance('INI')->stringToObject($data);
		}

		parent::__construct($data);

	}

	/**
	 * Get a parameter
	 * 
	 * @param string $name The name of the parameter
	 * @param mixed $default The default value of the parameter
	 * 
	 * @return mixed The value of the parameter
	 * 
	 * @since 1.0.0
	 */
	public function get($name, $default = null) {
		$name = (string) $name;

		if (preg_match('/\.$/', $name)) {

			$values = array();

			foreach ($this as $key => $value) {
				if (strpos($key, $name) === 0) {
					$values[substr($key, strlen($name))] = $value;
				}
			}

			if (!empty($values)) {
				return $values;
			}

		} else if ($this->offsetExists($name)) {
			return $this->offsetGet($name);
		}

		return $default;
	}
	
	/**
	 * Set a parameter
	 * 
	 * @param string $name The name of the parameter to set
	 * @param mixed $value The value of the parameter to set
	 * 
	 * @return ParameterData return $this for chaining support 
	 * 
	 * @since 1.0.0
	 */
	public function set($name, $value) {
		$name = (string) $name;

		if (preg_match('/\.$/', $name)) {

			$values = is_object($value) ? get_object_vars($value) : is_array($value) ? $value : array();

			foreach ($values as $key => $val) {
				$this->offsetSet($name.$key, $val);
			}

		} else {
			$this->offsetSet($name, $value);
		}

		return $this;
	}

	/**
	 * Remove a parameter
	 * 
	 * @param string $name The name of the parameter
	 * 
	 * @return ParameterData return $this for chaining support
	 * 
	 * @since 1.0.0
	 */
	public function remove($name) {
		$name = (string) $name;

		if (preg_match('/\.$/', $name)) {

			$keys = array();

			foreach ($this as $key => $value) {
				if (strpos($key, $name) === 0) {
					$keys[] = $key;
				}
			}

			foreach ($keys as $key) {
				$this->offsetUnset($key);
			}

		} else {
			$this->offsetUnset($name);
		}

		return $this;
	}

	/**
	 * Load an associative array of values
	 * 
	 * @param array $array The values
	 * 
	 * @return ParameterData return $this for chaining support
	 * 
	 * @since 1.0.0
	 */
	public function loadArray($array) {

		foreach ($array as $name => $value) {
			$this->offsetSet($name, $value);
		}

		return $this;
	}

	/**
	 * Load accessible non-static variables of an object 
	 * 
	 * @param object $object The object be to loaded
	 * 
	 * @return ParameterData return $this for chaining support
	 * 
	 * @since 1.0.0
	 */
	public function loadObject($object) {

		if (is_object($object)) {
			foreach (get_object_vars($object) as $name => $value) {
				$this->offsetSet($name, $value);
			}
		}

		return $this;
	}

}