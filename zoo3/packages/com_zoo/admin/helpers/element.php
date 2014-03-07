<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * A class that contains element helper functions
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ElementHelper extends AppHelper{

	/**
	 * Class constructor
	 *
	 * @param string $app App instance.
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$app->loader->register('Element', 'elements:element/element.php');
	}

	/**
	 * Returns an array of all Elements.
	 *
	 * @return array elements
	 *
	 * @since 2.0
	 */
	public function getAll(){

		$elements = array();

		foreach ($this->app->path->dirs('elements:') as $type) {

			if ($type != 'element' && is_file($this->app->path->path("elements:$type/$type.php"))) {
				if ($element = $this->create($type)) {
					if ($element->getMetaData('hidden') != 'true') {
						$elements[] = $element;
					}
				}
			}
		}

		return $elements;
	}

	/**
	 * Creates element of given type
	 *
	 * @param string $type The type to create
	 *
	 * @return Element the created element
	 *
	 * @since 2.0
	 */
	public function create($type) {

		// load element class
		$elementClass = 'Element'.$type;
		if (!class_exists($elementClass)) {
			$this->app->loader->register($elementClass, "elements:$type/$type.php");
		}

		if (!class_exists($elementClass)) {
			return false;
		}

		$testClass = new ReflectionClass($elementClass);

		if ($testClass->isAbstract()) {
			return false;
		}

		return new $elementClass($this->app);

	}

	/**
	 * Separates the passed element values with a separator
	 *
	 * @param string $separated_by The separator
	 * @param array $values the values to separate
	 *
	 * @return string The imploded string
	 *
	 * @since 2.0
	 */
	public function applySeparators($separated_by, $values) {

		if (!is_array($values)) {
			$values = array($values);
		}

		$separator = '';
		$tag = '';
		$enclosing_tag = '';
		if ($separated_by) {
			if (preg_match('/separator=\[(.*)\]/U', $separated_by, $result)) {
				$separator = $result[1];
			}

			if (preg_match('/tag=\[(.*)\]/U', $separated_by, $result)) {
				$tag = $result[1];
			}

			if (preg_match('/enclosing_tag=\[(.*)\]/U', $separated_by, $result)) {
				$enclosing_tag = $result[1];
			}
		}

		if (empty($separator) && empty($tag) && empty($enclosing_tag)) {
			$separator = ', ';
		}

		if (!empty($tag)) {
			foreach ($values as $key => $value) {
				$values[$key] = sprintf($tag, $values[$key]);
			}
		}

		$value = implode($separator, $values);

		if (!empty($enclosing_tag)) {
			$value = sprintf($enclosing_tag, $value);
		}

		return $value;
	}

}