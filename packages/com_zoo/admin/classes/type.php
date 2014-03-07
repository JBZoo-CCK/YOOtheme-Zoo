<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class that represents a Type
 *
 * @package Component.Classes
 */
class Type {

    /**
     * Id of the type
     *
     * @var string
     * @since 2.0
     */
	public $id;

    /**
     * Identifier for the type, same as Type::$id
     *
     * @var string
     * @since 2.0
     */
	public $identifier;

    /**
     * A reference to the global App object
     *
     * @var App
     * @since 2.0
     */
	public $app;

    /**
     * Type configuration
     *
     * @var JSONData
     * @since 2.0
     */
	public $config;

    /**
     * The application the type belongs to
     *
     * @var Application
     * @since 2.0
     */
	protected $_application;

    /**
     * The list of elements of this type
     *
     * @var array
     * @since 2.0
     */
	protected $_elements;

    /**
     * Configuration for the core elements
     *
     * @var JSONData
     * @since 2.0
     */
	protected $_core_elements_config;

	/**
	 * Class Constructor
	 *
	 * @param string 	  $id          Type idenfifier
	 * @param Application $application The Application object
	 */
	public function __construct($id, $application = null) {

		// init vars
		$this->app = App::getInstance('zoo');

		// init vars
		$this->id = $id;
		$this->identifier = $id;
		$this->_application = $application;

		$this->config = $this->app->data->create(($path = $this->getConfigFile() and JFile::exists($path)) ? file_get_contents($path) : null);

	}

	/**
	 * Get the application object
	 *
	 * @return Application The application
	 *
	 * @since 2.0
	 */
	public function getApplication() {
		return $this->_application;
	}

	/**
	 * Get the Type name
	 *
	 * @return string The name of the type
	 *
	 * @since 2.0
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Get an element from the type
	 *
	 * @param  string $identifier The element identifier
	 *
	 * @return Element             The element requested
	 *
	 * @since 2.0
	 */
	public function getElement($identifier) {

		// has element already been loaded?
		if (!$element = isset($this->_elements[$identifier]) ? $this->_elements[$identifier] : null) {
			if ($config = $this->getElementConfig($identifier)) {
				if ($element = $this->app->element->create((string) $config->type)) {
					$element->identifier = $identifier;
					$element->config = $config;
					$this->_elements[$identifier] = $element;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		$element = clone($element);
		$element->setType($this);

		return $element;
	}

	/**
	 * Get the configuration for a single element
	 *
	 * @param  string $identifier The id of the element
	 *
	 * @return JSONData             The configuration object
	 *
	 * @since 2.0
	 */
	public function getElementConfig($identifier) {

		if (isset($this->elements[$identifier])) {
			return $this->app->data->create($this->elements[$identifier]);
		}

		$core_config = $this->getCoreElementsConfig();
		if (isset($core_config[$identifier])) {
			return $this->app->data->create($core_config[$identifier]);
		}

		return null;
	}

	/**
	 * Get the non-core elements
	 *
	 * @return array The list of elements
	 *
	 * @since 2.0
	 */
	public function getElements() {
		if (!$this->elements) {
			$this->elements = array();
		}
		return $this->_getElements(array_keys(array_diff_key($this->elements, $this->getCoreElementsConfig())));
	}

	/**
	 * Get a list of elements filtered by type
	 *
	 * @return array The element list
	 *
	 * @since 3.1
	 */
	public function getElementsByType($type) {
		return array_filter($this->getElements(), create_function('$element', 'return $element->getElementType() == "'.$type.'";'));
	}

	/**
	 * 	Get the core elements
	 *
	 * @return array The list of core elements
	 */
	public function getCoreElements() {
		return $this->_getElements(array_keys($this->getCoreElementsConfig()));
	}

	/**
	 * Get the list of elements given a list of identifiers
	 *
	 * @param  array $identifiers The list of ids
	 *
	 * @return array              The list of elements
	 *
	 * @since 2.0
	 */
	protected function _getElements($identifiers) {
		if ($identifiers) {
			$elements = array();
			foreach ($identifiers as $identifier) {
				if ($element = $this->getElement($identifier)) {
					$elements[$identifier] = $element;
				}
			}
			return $elements;
		}

		return array();
	}

	/**
	 * Get the configuration for the core elements
	 *
	 * @return JSONData The core element configuration
	 *
	 * @since 2.0
	 */
	public function getCoreElementsConfig() {
		if (!$this->_core_elements_config) {
			$config = $this->app->data->create(file_get_contents($this->app->path->path('elements:core.config')))->get('elements');
			$this->_core_elements_config = $this->app->event->dispatcher->notify($this->app->event->create($this, 'type:coreconfig')->setReturnValue($config))->getReturnValue();
		}

		return $this->_core_elements_config;
	}

	/**
	 * Get a list of the submittable elements
	 *
	 * @return array The list of submittable elements
	 *
	 * @since 2.0
	 */
	public function getSubmittableElements() {
		return	array_filter(array_merge($this->getElements(), $this->getCoreElements()), create_function('$element', 'return $element instanceof iSubmittable;'));
	}

	/**
	 * Clear the loaded list of elements
	 *
	 * @return Type $this for chaining support
	 *
	 * @since 2.0
	 */
	public function clearElements() {
		$this->_elements = null;
		return $this;
	}

	/**
	 * Get the configuration file path
	 *
	 * @param  string $id The type id (default: the current type id)
	 *
	 * @return string     The path to the file
	 *
	 * @since 2.0
	 */
	public function getConfigFile($id = null) {

		$id = ($id !== null) ? $id : $this->id;

		if ($id && ($path = $this->app->path->path($this->_application->getResource().'types'))) {
			return $path.'/'.$id.'.config';
		}

		return null;

	}

	/**
	 * Bind the data passed to the Type
	 *
	 * @param  array $data The data to bind
	 *
	 * @return Type       $this for chaining support
	 *
	 * @since 2.0
	 */
	public function bind($data) {

		if (isset($data['identifier'])) {

			// check identifier
			if ($data['identifier'] == '' || $data['identifier'] != $this->app->string->sluggify($data['identifier'], true)) {
				throw new TypeException('Invalid identifier');
			}

			$this->identifier = $data['identifier'];
		}

		if (isset($data['name'])) {

			// check name
			if ($data['name'] == '') {
				throw new TypeException('Invalid name');
			}

			$this->name = $data['name'];
		}

		return $this;
	}

	/**
	 * Bind the data passed to the elements of the type
	 *
	 * @param  array $data The data to bind
	 *
	 * @return Type       $this for chaining support
	 *
	 * @since 2.0
	 */
	public function bindElements($data) {

		if (isset($data['elements'])) {
			$this->elements = $data['elements'];
		}

		$this->clearElements();
		return $this;
	}

	/**
	 * Save the current type
	 *
	 * @return Type $this for chaining support
	 *
	 * @since 2.0
	 */
	public function save() {

		// trigger before save event
		$this->app->event->dispatcher->notify($this->app->event->create($this, 'type:beforesave'));

		$old_identifier = $this->id;
		$rename = false;

		if (empty($this->id)) {

			// check identifier
			if (file_exists($this->getConfigFile($this->identifier))) {
				throw new TypeException('Identifier already exists');
			}

		} else if ($old_identifier != $this->identifier) {

			// check identifier
			if (file_exists($this->getConfigFile($this->identifier))) {
				throw new TypeException('Identifier already exists');
			}

			// rename xml file
			if (!JFile::move($this->getConfigFile(), $this->getConfigFile($this->identifier))) {
				throw new TypeException('Renaming config file failed');
			}

			$rename = true;

		}

		// update id
		$this->id = $this->identifier;

		// save config file
		if ($file = $this->getConfigFile()) {
			$config_string = (string) $this->config;
			if (!JFile::write($file, $config_string)) {
				throw new TypeException('Writing type config file failed');
			}
		}

		// rename related items
		if ($rename) {

			// get database
			$db = $this->app->database;

			$group = $this->getApplication()->getGroup();

			// update childrens parent category
			$query = "UPDATE ".ZOO_TABLE_ITEM." as a, ".ZOO_TABLE_APPLICATION." as b"
			    	." SET a.type=".$db->quote($this->identifier)
				    ." WHERE a.type=".$db->quote($old_identifier)
					." AND a.application_id=b.id"
					." AND b.application_group=".$db->quote($group);
			$db->query($query);
		}

		// trigger after save event
		$this->app->event->dispatcher->notify($this->app->event->create($this, 'type:aftersave'));

		return $this;
	}

	/**
	 * Delete the type
	 *
	 * @return Type $this for chaining support
	 *
	 * @since 2.0
	 */
	public function delete() {

		// check if type has items
		if ($this->app->table->item->getTypeItemCount($this)) {
			throw new TypeException('Cannot delete type, please delete the related items first');
		}

		// delete config file
		if (!JFile::delete($this->getConfigFile())) {
			throw new TypeException('Deleting config file failed');
		}

		return $this;
	}

	/**
	 * Magig method to check if the type configuration has a particular key
	 *
	 * @param  string  $name The key to search for
	 *
	 * @return boolean       If the key is in the type configuration
	 *
	 * @since 2.0
	 */
	public function __isset($name) {
		return $this->config->has($name);
	}

	/**
	 * Magic method to get a configuration value
	 *
	 * @param  string $name The configuration key
	 *
	 * @return mixed       The configuration value
	 *
	 * @since 2.0
	 */
	public function __get($name) {
		return $this->config->get($name);
	}

 	/**
 	 * Magic method to set a configuration value
 	 *
 	 * @param string $name  The configuration key
 	 * @param mixed  $value The value to set
 	 *
 	 * @since 2.0
 	 */
	public function __set($name, $value) {
		$this->config->set($name, $value);
	}

 	/**
 	 * Magic method to remove a configuration key
 	 *
 	 * @param string $name The configuration key
 	 *
 	 * @since 2.0
 	 */
	public function __unset($name) {
		$this->config->remove($name);
	}

}

/**
 * Exception for the Type class
 *
 * @see Type
 */
class TypeException extends AppException {}