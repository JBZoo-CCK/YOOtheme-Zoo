<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class that deals with a template for an application
 *
 * @package Component.Classes
 */
class AppTemplate {

    /**
     * The template name
     *
     * @var string
     * @since 2.0
     */
	public $name;

    /**
     * The template resource (path to the folder)
     *
     * @var string
     * @since 2.0
     */
	public $resource;

    /**
     * Name of the file for the metadata information
     *
     * @var string
     * @since 2.0
     */
	public $metaxml_file = "template.xml";

    /**
     * Reference to the global App object
     *
     * @var App
     * @since 2.0
     */
	public $app;

    /**
     * The metadata in xml format
     *
     * @var SimpleXMLElement
     * @since 2.0
     */
	public $_metaxml;

	/**
	 * Class Constructor
	 *
	 * @param string $name     The name of the template
	 * @param string $resource The path to the folder
	 */
	public function __construct($name, $resource) {
		// set vars
		$this->name = $name;
		$this->resource = rtrim($resource, '\/') . '/';
	}

	/**
	 * Get the path to the template
	 *
	 * @return string The path
	 *
	 * @since 2.0
	 */
	public function getPath() {
		return $this->app->path->path($this->resource);
	}

	/**
	 * Get the URI to the template. Used for loading css/js files
	 *
	 * @return string The uri
	 *
	 * @since 2.0
	 */
	public function getURI() {
		return $this->app->path->url($this->resource);
	}

	/**
	 * Get the template parameter for
	 *
	 * @param  boolean $global If the global parameters have to be added
	 *
	 * @return ParameterForm          The parameter form object
	 *
	 * @since 2.0
	 */
	public function getParamsForm($global = false) {

		// get parameter xml file
		if ($file = $this->app->path->path($this->resource.$this->metaxml_file)) {

			// set xml file
			$xml = $file;

			// parse xml and add global
			if ($global) {
				$xml = simplexml_load_file($file);
				foreach ($xml->params as $param) {
					foreach ($param->children() as $element) {
						$type = (string) $element->attributes()->type;

						if (in_array($type, array('list', 'radio', 'text'))) {
							$element->attributes()->type = $type.'global';
						}
					}
				}
			}

			// get form
			return $this->app->parameterform->create(array($xml));
		}

		return null;
	}

	/**
	 * Get the metadata information as an array
	 *
	 * @param  string $key They key you would like to get. Defailt: all
	 *
	 * @return array      The information
	 *
	 * @since 2.0
	 */
	public function getMetaData($key = null) {

		$data = array();
		$xml  = $this->getMetaXML();

		if (!$xml) {
			return false;
		}

		if ($xml->getName() != 'template') {
			return false;
		}
		$data['name'] 		  = (string) $xml->name;
		$data['creationdate'] = $xml->creationDate ? (string) $xml->creationDate : 'Unknown';
		$data['author'] 	  = $xml->author ? (string) $xml->author : 'Unknown';
		$data['copyright'] 	  = (string) $xml->copyright;
		$data['authorEmail']  = (string) $xml->authorEmail;
		$data['authorUrl']    = (string) $xml->authorUrl;
		$data['version'] 	  = (string) $xml->version;
		$data['description']  = (string) $xml->description;

		$data['positions'] = array();
		if (isset($xml->positions)) {
			foreach ($xml->positions->children() as $element) {
				$data['positions'][] = (string) $element;
			}
		}

		$data = $this->app->data->create($data);

		return $key == null ? $data : $data->get($key);
	}

	/**
	 * Get the xml of the metadata file
	 *
	 * @return SimpleXMLElement The xml object
	 *
	 * @since 2.0
	 */
	public function getMetaXML() {

		if (empty($this->_metaxml)) {
			$this->_metaxml = simplexml_load_file($this->app->path->path($this->resource . $this->metaxml_file));
		}

		return $this->_metaxml;
	}

}

/**
 * Exception for templates
 *
 * @see AppTemplate
 */
class TemplateException extends AppException {}