<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Access components configuration
 *
 * @package Framework.Classes
 * @since 1.0.0
 */
class AppComponent {

	/**
	 * Reference to the global App class
	 *
	 * @var App
	 * @since 1.0.0
	 */
	public $app;

	/**
	 * Name of the component
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $name;

	/**
	 * The component object
	 *
	 * @var object
	 * @since 1.0.0
	 */
	protected $_component;

	/**
	 * The component parameters
	 *
	 * @var JSONData
	 * @since 1.0.0
	 */
	protected $_params;

	/**
	 * Class Constructor
	 *
	 * @param App $app A reference to an App object
	 * @param string $name The name of the component
	 */
	public function __construct($app, $name) {
		$this->app        = $app;
		$this->name       = $name;
		$this->_component = JComponentHelper::getComponent($name);
		$this->_params    = $app->parameter->create($this->_component->params);
	}

	/**
	 * Get link to component related resources
	 *
	 * @param array $query HTTP query options
	 * @param boolean $xhtml Replace & by &amp; for xml compilance
	 * @param boolean $ssl Secure state for the resolved URI. [1 => Make URI secure using global secure site URI, 0 => Leave URI in the same secure state as it was passed to the function, -1 => Make URI unsecure using the global unsecure site URI]
	 *
	 * @return string The link to the resource
	 *
	 * @since 1.0.0
	 */
	public function link($query = array(), $xhtml = true, $ssl = null) {

		// prepend option to query
		$query = array_merge(array('option' => $this->name), $query);

		return JRoute::_('index.php?'.http_build_query($query, '', '&'), $xhtml, $ssl);
	}

	/**
	 * Returns a configuration property of the object or the default value if the property is not set.
	 *
	 * @param string $property The name of the property
	 * @param mixed $default The default value
	 *
	 * @return mixed The value of the property
	 *
	 * @since 1.0.0
	 */
	public function get($property, $default = null) {
		return $this->_params->get($property, $default);
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param string $property The name of the property
	 * @param mixed $value The value of the property
	 *
	 * @return mixed The previous value of the property
	 *
	 * @since 1.0.0
	 */
	public function set($property, $value = null) {
		return $this->_params->set($property, $value);
	}

	/**
	 * Save configuration properties to the database
	 *
	 * @since 1.0.0
	 */
	public function save() {

		// init vars
		$table = $this->app->table->get('extensions', '#__');
		$table->key = 'extension_id';

		$component = $table->get($this->_component->id);

		// save properties
		$component->params = (string) $this->_params;
		$table->save($component);
		
	}

}