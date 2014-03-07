<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The alphaindex helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class AlphaindexHelper extends AppHelper {

	/**
	 * Get an AppAlphaindex instance
	 *
	 * @param string $path Path to xml alphaindex definition.
	 *
	 * @return AppAlphaindex
	 *
	 * @since 2.0
	 */
	public function create($path) {
		return $this->app->object->create('AppAlphaindex', array($path));
	}

}

/**
 * The AppAlphaindex Class. Provides Alphaindex functionality.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class AppAlphaindex {

	/**
	 * App instance
	 *
	 * @var App
	 * @since 2.0
	 */
	public $app;

	/**
	 * The character index
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_index = array();

	/**
	 * The object key mapping
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_objects = array();

	/**
	 * The "other" character
	 *
	 * @var string
	 * @since 2.0
	 */
	protected $_other = '#';

	/**
	 * Class constructor
	 *
	 * @param string $path Path to xml alphaindex definition.
	 * @since 2.0
	 */
	public function __construct($path) {
		if ($xml = simplexml_load_file($path)) {

			// add other character
			if ($xml->attributes()->other) {
				$this->_other = (string) $xml->attributes()->other;
			}

			// add characters
			foreach ($xml->children() as $option) {
				if (!in_array((string) $option, $this->_index)) {
					$key = $option->attributes()->value ? (string) $option->attributes()->value : (string) $option;
					$this->_index[$key] = (string) $option;
				}
			}
		}
	}

	/**
	 * Retrieve character index.
	 *
	 * @param boolean $other Include other character in index
	 *
	 * @return array The index
	 *
	 * @since 2.0
	 */
	public function getIndex($other = false) {

		$index = $this->_index;

		$key = $other ? false : array_search($this->_other, $index);

		if ($key !== false) {
			unset($index[$key]);
		}

		return $index;
	}

	/**
	 * Retrieve character for items which are not indexed, usually #.
	 *
	 * @return string The other character
	 *
	 * @since 2.0
	 */
	public function getOther() {
		return $this->_other;
	}

	/**
	 * Retrieve alpha char from value.
	 *
	 * @param string $value The value to look for in the index
	 *
	 * @return string The alpha character
	 *
	 * @since 2.0
	 */
	public function getChar($value) {
		return isset($this->_index[$value]) ? $this->_index[$value] : '';
	}

	/**
	 * Retrieve objects which match a character in index.
	 *
	 * @param string $char Index character
	 * @param string $class_name Retrieve only objects of a certain class
	 *
	 * @return array The objects
	 *
	 * @since 2.0
	 */
	public function getObjects($char, $class_name = null) {

		$key = array_search($char, $this->_index);

		if ($key !== false && isset($this->_objects[$key])) {

			if ($class_name !== null) {
				return array_filter($this->_objects[$key], create_function('$object', 'return $object instanceof '.$class_name.';'));
			}

			return $this->_objects[$key];
		}

		return array();
	}

	/**
	 * Add objects to index.
	 *
	 * @param array $objects Object array
	 * @param string $property Object property to use for indexing
	 *
	 * @return void
	 *
	 * @since 2.0
	 */
	public function addObjects($objects, $property) {

		$index = $this->getIndex();

		foreach ($objects as $object) {
			if (isset($object->$property)) {

				$char = $this->app->string->strtolower($this->app->string->substr($object->$property, 0, 1));
				$key = array_search($char, $index);

				if ($key !== false) {
					$this->_objects[$key][] = $object;
				} else {
					$this->_objects[array_search($this->getOther(), $this->_index)][] = $object;
				}
			}
		}

		return $this;
	}

	/**
	 * Render the alphaindex.
	 *
     * @param Application The app to use as base for the routes
	 * @return string Alphaindex html
	 *
	 * @since 2.0
	 */
	public function render(Application $app = null) {

        if (!$app) {
            $app = $this->app->zoo->getApplication();
        }

		// check if index is empty
		if (empty($this->_index)) {
			return '';
		}

        $html = array();

		// create html
		foreach ($this->_index as $key => $char) {
			if (isset($this->_objects[$key]) && count($this->_objects[$key])) {
				$html[] = '<a href="'.JRoute::_($this->app->route->alphaindex($app->id, $key)).'" title="'.$char.'">'.$char.'</a>';
			} else {
				$html[] = '<span title="'.$char.'">'.$char.'</span>';
			}
		}

		return implode("\n", $html);
	}

}