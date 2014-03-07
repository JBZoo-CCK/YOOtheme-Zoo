<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: Element
		The Element abstract class
*/
abstract class Element {

    /*
       Variable: $identifier
         Element identifier.
    */
	public $identifier;

    /*
		Variable: app
			App instance.
    */
	public $app;

    /*
       Variable: $config
         Config AppData object.
    */
	public $config;

    /*
       Variable: $_type
         Elements related type object.
    */
	protected $_type;

    /*
       Variable: $_item
         Elements related item object.
    */
	protected $_item;

    /*
       Variable: $_callbacks
         Element callbacks.
    */
	protected $_callbacks = array();

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// set app
		$this->app = App::getInstance('zoo');

		$this->config = $this->app->data->create();

	}

	/*
		Function: getElementType
			Gets the elements type.

		Returns:
			string - the elements type
	*/
	public function getElementType() {
		return strtolower(str_replace('Element', '', get_class($this)));
	}

	/*
		Function: get
			Gets the elements data.

		Returns:
			Mixed - the elements data
	*/
	public function get($key, $default = null) {
		return $this->_item->elements->find("{$this->identifier}.{$key}", $default);
	}

	/*
		Function: set
			Sets the elements data.

		Returns:
			Element - this
	*/
	public function set($key, $value) {
		$this->_item->elements[$this->identifier][$key] = $value;
		return $this;
	}

	/*
		Function: bindData
			Set data through data array.

		Parameters:
			$data - array

		Returns:
			Void
	*/
	public function bindData($data = array()) {
		if (isset($this->_item)) {
			$this->_item->elements->set($this->identifier, $data);
		}
	}

	/*
		Function: data
			Gets data array.

		Returns:
			Array - data
	*/
	public function data() {
		if (isset($this->_item)) {
			return $this->_item->elements->get($this->identifier);
		}

		return array();
	}

	/* @deprecated as of version 2.5 beta */
	public function getElementData() {
		return $this->app->data->create($this->data());
	}

	/* @deprecated as of version 2.5 beta */
	public function getConfig() {
		return $this->config;
	}

	/*
		Function: getLayout
			Get element layout path and use override if exists.

		Returns:
			String - Layout path
	*/
	public function getLayout($layout = null) {

		// init vars
		$type = $this->getElementType();

		// set default
		if ($layout == null) {
			$layout = "{$type}.php";
		}

		// find layout
		return $this->app->path->path("elements:{$type}/tmpl/{$layout}");

	}

	/*
		Function: getSearchData
			Get elements search data.

		Returns:
			String - Search data
	*/
	public function getSearchData() {
		return null;
	}

	/*
		Function: getItem
			Get related item object.

		Returns:
			Item - item object
	*/
	public function getItem() {
		return $this->_item;
	}

	/*
		Function: getType
			Get related type object.

		Returns:
			Type - type object
	*/
	public function getType() {
		return $this->_type;
	}

	/*
		Function: getGroup
			Get element group.

		Returns:
			string - group
	*/
	public function getGroup() {
		return $this->getMetadata('group');
	}

	/*
		Function: setItem
			Set related item object.

		Parameters:
			$item - item object

		Returns:
			Void
	*/
	public function setItem($item) {
		$this->_item = $item;
	}

	/*
		Function: setType
			Set related type object.

		Parameters:
			$type - type object

		Returns:
			Void
	*/
	public function setType($type) {
		$this->_type = $type;
	}

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$value = $this->get('value', $this->config->get('default'));
		return !empty($value);
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// render layout
		if ($layout = $this->getLayout()) {
			return $this->renderLayout($layout, array('value' => $this->get('value')));
		}

		return $this->get('value');
	}

	/*
		Function: renderLayout
			Renders the element using template layout file.

	   Parameters:
            $__layout - layouts template file
	        $__args - layouts template file args

		Returns:
			String - html
	*/
	protected function renderLayout($__layout, $__args = array()) {

		// init vars
		if (is_array($__args)) {
			foreach ($__args as $__var => $__value) {
				$$__var = $__value;
			}
		}

		// render layout
		$__html = '';
		ob_start();
		include($__layout);
		$__html = ob_get_contents();
		ob_end_clean();

		return $__html;
	}

	/*
	   Function: edit
	       Renders the edit form field.
		   Must be overloaded by the child class.

	   Returns:
	       String - html
	*/
	abstract public function edit();

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		return $this;
	}

	/*
		Function: loadConfigAssets
			Load elements css/js config assets.

		Returns:
			Element
	*/
	public function loadConfigAssets() {
		return $this;
	}

	/*
		Function: registerCallback
			Register a callback function.

		Returns:
			Void
	*/
	public function registerCallback($method) {
		if (!in_array(strtolower($method), $this->_callbacks)) {
			$this->_callbacks[] = strtolower($method);
		}
	}

	/*
		Function: callback
			Execute elements callback function.

		Returns:
			Mixed
	*/
	public function callback($method, $args = array()) {

		// try to call a elements class method
		if (in_array(strtolower($method), $this->_callbacks) && method_exists($this, $method)) {
			// call method
			$res = call_user_func_array(array($this, $method), $args);
			// output if method returns a string
			if (is_string($res)) {
				echo $res;
			}
		}
	}

	/*
		Function: getConfigForm
			Get parameter form object to render input form.

		Returns:
			Parameter Object
	*/
	public function getConfigForm() {

		// get form
		$form = $this->app->parameterform->create();

		// get config xml files
		$params = array();
		$class = new ReflectionClass($this);
		while ($class !== false) {
			$type = $class->getName() == 'Element' ? 'element' : strtolower(str_replace('Element', '', $class->getName()));
			if ($xml = $this->app->path->path("elements:$type/$type.xml")) {
				array_unshift($params, $xml);
			}
			$class = $class->getParentClass();
		}

		// trigger configparams event
		$params = $this->app->event->dispatcher->notify($this->app->event->create($this, 'element:configparams')->setReturnValue($params))->getReturnValue();

		// skip if there are no config files
		if (empty($params)) {
			return null;
		}

		// add config xml files
		foreach ($params as $xml) {
			$form->addXML($xml);
		}

		// set values
		$form->setValues($this->config);

		// add reference to element
		$form->element = $this;

		// trigger configform event
		$this->app->event->dispatcher->notify($this->app->event->create($this, 'element:configform', compact('form')));

		return $form;

	}

	/*
		Function: getMetaData
			Get elements xml meta data.

		Returns:
			Array - Meta information
	*/
	public function getMetaData($key = null) {

		$data = array();
		$type = $this->getElementType();
		$xml  = $this->loadXML();

		if (!$xml) {
			return false;
		}

		$data['type'] 		  = $xml->attributes()->type ? (string) $xml->attributes()->type : 'Unknown';
		$data['group'] 		  = $xml->attributes()->group ? (string) $xml->attributes()->group : 'Unknown';
		$data['hidden'] 	  = $xml->attributes()->hidden ? (string) $xml->attributes()->hidden : 'false';
        $data['trusted'] 	  = $xml->attributes()->trusted ? (string) $xml->attributes()->trusted : 'false';
		$data['orderable']	  = $xml->attributes()->orderable ? (string) $xml->attributes()->orderable : 'false';
		$data['name'] 		  = (string) $xml->name;
		$data['creationdate'] = $xml->creationDate ? (string) $xml->creationDate : 'Unknown';
		$data['author'] 	  = $xml->author ? (string) $xml->author : 'Unknown';
		$data['copyright'] 	  = (string) $xml->copyright;
		$data['authorEmail']  = (string) $xml->authorEmail;
		$data['authorUrl'] 	  = (string) $xml->authorUrl;
		$data['version'] 	  = (string) $xml->version;
		$data['description']  = (string) $xml->description;

		$data = $this->app->data->create($data);

		return $key == null ? $data : $data->get($key);
	}

	/**
	 * Retrieve Element XML file info
	 *
	 * @return Object the XML loaded file
	 *
	 * @since 3.0.9
	 */
	public function loadXML() {

		$type = $this->getElementType();
		return simplexml_load_file($this->app->path->path("elements:$type/$type.xml"));
	}

	/*
		Function: getControlName
			Gets the controle name for given name.

		Returns:
			String - the control name
	*/
	public function getControlName($name, $array = false) {
		return "elements[{$this->identifier}][{$name}]" . ($array ? "[]":"");
	}

	/*
    	Function: canAccess
    	  Check if element is accessible with users access rights.

	   Returns:
	      Boolean - True, if access granted
 	*/
	public function canAccess($user = null) {
		return $this->app->user->canAccess($user, $this->config->get('access', $this->app->joomla->getDefaultAccess()));
	}

	/*
		Function: getPath
			Get path to element's base directory.

		Returns:
			String - Path
	*/
	public function getPath() {
		return $this->app->path->path("elements:".$this->getElementType());
	}

	/*
		Function: __get
			Magic getter for deprecated _data and _config properties

		Returns:
			misc
	*/
	public function __get($name) {
		switch ($name) {
			case '_data':
				return $this->getElementData();
			case '_config':
				return $this->config;
		}
	}

}

// Declare the interface 'iSubmittable'
interface iSubmittable {

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
    public function renderSubmission($params = array());

    /*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
    public function validateSubmission($value, $params);
}

// deprecated as of version 2.5.7
interface iSubmissionUpload {

	// deprecated as of version 2.5.7 use beforeSubmissionSave callback instead
    public function doUpload();
}

/*
	Class: ElementException
*/
class ElementException extends AppException {}