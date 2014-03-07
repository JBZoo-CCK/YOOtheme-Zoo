<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper do deal with request variables. Wrapper for JRequest
 *
 * @package Framework.Helpers
 */
class RequestHelper extends AppHelper {

	/**
	 * Name of the wrapper class
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $_class = 'JRequest';

	/**
	 * Get a variable from the request
	 *
	 * @param string $var The name of the variable
	 * @param string $type The type of the variale (string, int, float, bool, array, word, cmd)
	 * @param mixed $default The default value
	 *
	 * @return mixed The value of the variable
	 *
	 * @see JRequest::get()
	 *
	 * @since 1.0.0
	 */
    public function get($var, $type, $default = null) {

		// parse variable name
		extract($this->_parse($var));

		// get hash array, if name is empty
		if ($name == '') {
			return $this->_call(array($this->_class, 'get'), array($hash));
		}

		// access a array value ?
		if (strpos($name, '.') !== false) {

			$parts = explode('.', $name);
			$array = $this->_call(array($this->_class, 'getVar'), array(array_shift($parts), $default, $hash, 'array'));

			foreach ($parts as $part) {

				if (!is_array($array) || !isset($array[$part])) {
					return $default;
				}

				$array =& $array[$part];
			}

			return $array;
		}

		return $this->_call(array($this->_class, 'getVar'), array($name, $default, $hash, $type));
    }

	/**
	 * Set a request variable value
	 *
	 * @param string $var The variable name (hash:name)
	 * @param mixed $value The value to set
	 *
	 * @return RequestHelper $this for chaining support
	 *
	 * @since 1.0.0
	 */
    public function set($var, $value = null) {

		// parse variable name
		extract($this->_parse($var));

		if (!empty($name)) {

			// set default hash to method
			if ($hash == 'default') {
				$hash = 'method';
			}

			// set a array value ?
			if (strpos($name, '.') !== false) {

				$parts = explode('.', $name);
				$name  = array_shift($parts);
				$array =& $this->_call(array($this->_class, 'getVar'), array($name, array(), $hash, 'array'));
				$val   =& $array;

				foreach ($parts as $i => $part) {

					if (!isset($array[$part])) {
						$array[$part] = array();
					}

					if (isset($parts[$i + 1])) {
						$array =& $array[$part];
					} else {
						$array[$part] = $value;
					}
				}

				$value = $val;
			}

			$this->_call(array($this->_class, 'setVar'), array($name, $value, $hash));
		}

		return $this;
    }

	/**
	 * Map all the methods to the mapped class
	 *
	 * @param string $method The name of the method
	 * @param array $args The list of arguments to pass on to the method
	 *
	 * @return mixed The result of the call
	 *
	 * @see JRequest
	 *
	 * @since 1.0.0
	 */
    public function __call($method, $args) {
		return $this->_call(array($this->_class, $method), $args);
    }

	/**
	 * Parse a variable string
	 *
	 * @param string $var The variable string to parse
	 *
	 * @return array An array containing the hash and the name of the variable
	 *
	 * @since 1.0.0
	 */
	protected function _parse($var) {

	    // init vars
		$parts = explode(':', $var, 2);
		$count = count($parts);
		$name  = '';
		$hash  = 'default';

		// parse variable name
		if ($count == 1) {
			list($name) = $parts;
		} elseif ($count == 2) {
			list($hash, $name) = $parts;
		}

		return compact('hash', 'name');
    }

}