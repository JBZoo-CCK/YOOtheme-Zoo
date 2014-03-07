<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * ParmeterForm helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ParameterFormHelper extends AppHelper {

	/**
	 * Creates a parameter form instance
	 *
	 * @param array $args
	 * @return AppParameterForm
	 * @since 2.0
	 */
	public function create($args = array()) {
		$args = (array) $args;
		array_unshift($args, $this->app);
		return $this->app->object->create('AppParameterForm', $args);
	}

	/**
	 * Convert params to AppData
	 *
	 * @param JParameter|AppParameterForm|array $params
	 * @return AppData the converted params
	 * @since 2.0
	 */
	public function convertParams($params = array()) {

		if ($params instanceof AppParameterForm) {
			$params = $params->getValues();
		}

		return $this->app->data->create($params);
	}

}

/**
 * Render parameter XML as HTML form.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class AppParameterForm {

	/**
	 * App instance
	 *
	 * @var App
	 * @since 2.0
	 */
	public $app;

	/**
	 * Array of values
	 *
	 * @var array
	 */
	protected $_values = array();

	/**
	 * The xml params object array, with each group as array key.
	 * @var array
	 */
	protected $_xml;

	/**
	 * Class constructor
	 *
	 * @param App $app The app instance
	 * @param string|SimpleXMLElement $xml The xml file path or the xml string or the SimpleXMLElement
	 * @since 2.0
	 */
	public function __construct($app, $xml = null) {

		// init vars
		$this->app = $app;

		$this->loadXML($xml);
	}

	/**
	 * Retrieve a form value
	 *
	 * @param string $name
	 * @param mixed $default
	 *
	 * @return mixed
	 * @since 2.0
	 */
	public function getValue($name, $default = null) {

		if (isset($this->_values[$name])) {
			return $this->_values[$name];
		}

		return $default;
	}

	/**
	 * Set a form value
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return self
	 * @since 2.0
	 */
	public function setValue($name, $value) {
		$this->_values[$name] = $value;
		return $this;
	}

	/**
	 * Retrieve form values
	 *
	 * @return array
	 * @since 2.0
	 */
	public function getValues() {
		return $this->_values;
	}

	/**
	 * Set form values
	 *
	 * @param array $values
	 *
	 * @return self
	 * @since 2.0
	 */
	public function setValues($values) {
		$this->_values = (array) $values;
		return $this;
	}

	/**
	 * Add a directory to search for field types
	 *
	 * @param string $path
	 *
	 * @return self
	 * @since 2.0
	 */
	public function addElementPath($path) {
		$this->app->path->register($path, 'fields');
		return $this;
	}

	/**
	 * Return number of params to render
	 *
	 * @param string $group Parameter group
	 *
	 * @return int Parameter count
	 * @since 2.0
	 */
	public function getParamsCount($group = '_default') {
		if (!isset($this->_xml[$group]) || !count($this->_xml[$group]->children())) {
			return false;
		}

		return count($this->_xml[$group]->children());
	}

	/**
	 * Get the number of params in each group
	 *
	 * @return array Array of all group names as key and parameter count as value
	 * @since 2.0
	 */
	public function getGroups() {
		if (!is_array($this->_xml)) {
			return false;
		}

		$results = array();

		foreach ($this->_xml as $name => $group) {
			$results[$name] = $this->getParamsCount($name);
		}

		return $results;
	}

	/**
	 * Sets the xml for a group
	 *
	 * @param SimpleXMLElement $xml
	 * @since 2.0
	 */
	public function setXML($xml) {
		if ($xml instanceof SimpleXMLElement) {

			if ($group = (string) $xml->attributes()->group) {
				$this->_xml[$group] = $xml;
			} else {
				$this->_xml['_default'] = $xml;
			}

			if ($path = (string) $xml->attributes()->addpath) {
				$this->addElementPath(JPATH_ROOT.$path);
			}
		}
	}

	/**
	 * Loads an xml file or formatted string and parses it
	 *
	 * @param string|SimpleXMLElement $xml The xml file path or the xml string or an SimpleXMLElement
	 *
	 * @return boolean true on success
	 * @since 2.0
	 */
	public function loadXML($xml) {

		$element = false;

		if ($xml instanceof SimpleXMLElement) {
			$element = $xml;
		}

		// load xml file or string ?
		if ($element || ($element = @simplexml_load_file($xml)) || ($element = simplexml_load_string($xml))) {
			if (isset($element->params)) {
				foreach ($element->params as $param) {
					$this->setXML($param);
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Adds an xml file or formatted string and parses it
	 *
	 * @param string|SimpleXMLElement $xml The xml file path or the xml string or an SimpleXMLElement
	 *
	 * @return boolean true on success
	 * @since 2.0
	 */
	public function addXML($xml) {

		$element = false;

		if ($xml instanceof SimpleXMLElement) {
			$element = $xml;
		}

		// load xml file or string ?
		if ($element or $element = @simplexml_load_file($xml) or $element = simplexml_load_string($xml)) {
			if (isset($element->params)) {
				foreach ($element->params as $params) {

					$group = $params->attributes()->group ? (string) $params->attributes()->group : '_default';

					if (!isset($this->_xml[$group])) {
						$this->_xml[$group] = new SimpleXMLElement('<params></params>');
					}

					foreach ($params->param as $param) {
						// Avoid parameters in the same group with the same name
						$existing_params = $this->_xml[$group]->children();
						foreach ($existing_params as $existing_param) {
							// If it exists already ( Skip array params, they can have the same name )
							if (((string) $existing_param->attributes()->name == (string) $param->attributes()->name)) {
								// remove the old and let it add the new
								$dom = dom_import_simplexml($existing_param);
								$dom->parentNode->removeChild($dom);
								continue;
							}
						}

						$this->_appendSimpleXMLElement($this->_xml[$group], $param);
					}

					if ($path = (string) $params->attributes()->addpath) {
						$this->addElementPath(JPATH_ROOT.$path);
					}
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Get the xml for a specific group or all groups
	 *
	 * @param string $group
	 * @return SimpleXMLElement|array|false Array of groups or the xml for a group
	 * @since 2.0
	 */
	public function getXML($group = null) {

		if (!$group) {
			return $this->_xml;
		}

		if (isset($this->_xml[$group])) {
			return $this->_xml[$group];
		}

		return false;
	}

	/**
	 * Render parameter HTML form
	 *
	 * @param string $control_name The name of the control, or the default text area if a setup file is not found
	 * @param string $group Parameter group
	 *
	 * @return string HTML output
	 * @since 2.0
	 */
	public function render($control_name = 'params', $group = '_default') {
		if (!isset($this->_xml[$group])) {
			return false;
		}

		$html = array('<ul class="parameter-form">');

		// add group description
		if ($description = (string) $this->_xml[$group]->attributes()->description) {
			$html[] = '<li class="description">'.JText::_($description).'</li>';
		}

		// add params
		foreach ($this->_xml[$group]->param as $param) {

			// init vars
			$type = (string) $param->attributes()->type;
			$name = (string) $param->attributes()->name;
			$value = $this->getValue((string) $param->attributes()->name, (string) $param->attributes()->default);

			$field = '<div class="field">'.$this->app->field->render($type, $name, $value, $param, array('control_name' => $control_name, 'parent' => $this)).'</div>';

			if ($type != 'hidden') {
				$html[] = '<li class="parameter">';

				$output = '&#160;';
				if ((string) $param->attributes()->label != '') {
					$attributes = array('for' => $control_name.$name);
					if ((string) $param->attributes()->description != '') {
						$attributes['class'] = 'hasTip';
						$attributes['title'] = JText::_($param->attributes()->label).'::'.JText::_($param->attributes()->description);
					}
					$output = sprintf('<label %s>%s</label>', $this->app->field->attributes($attributes), JText::_($param->attributes()->label));
				}

				$html[] = "<div class=\"label\">$output</div>";
				$html[] = $field;
				$html[] = '</li>';
			} else {
				$html[] = $field;
			}
		}

		$html[] = '</ul>';

		return implode("\n", $html);
	}

	protected function _appendSimpleXMLElement($parent, $child) {
		if (strlen(trim((string) $child)) == 0) {
			$xml = $parent->addChild($child->getName());
			foreach ($child->children() as $child_xml) {
				$this->_appendSimpleXMLElement($xml, $child_xml);
			}
		} else {
			$xml = $parent->addChild($child->getName(), (string) $child);
		}
		foreach ($child->attributes() as $n => $v) {
			$xml->addAttribute($n, $v);
		}
	}

}