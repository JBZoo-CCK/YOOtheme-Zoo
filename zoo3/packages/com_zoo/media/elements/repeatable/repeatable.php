<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: ElementRepeatable
       The repeatable element class
*/
abstract class ElementRepeatable extends Element implements Countable, SeekableIterator {

	/*
       Variable: $_position
         Stores the current data pointer.
    */
	private $_position = 0;

	/*
		Function: get
			Gets the elements data.

		Returns:
			Mixed - the elements data
	*/
	public function get($key, $default = null) {
		return parent::get("{$this->_position}.{$key}", $default);
	}

	/*
		Function: set
			Sets the elements data.

		Returns:
			Element - this
	*/
	public function set($key, $value) {
		$this->_item->elements[$this->identifier][$this->_position][$key] = $value;
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
			$this->_item->elements->set($this->identifier, array_merge((array) $data));
		}
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->_renderRepeatable('_edit');
	}

	/*
	   Function: _edit
	       Renders the repeatable edit form field.
		   Must be overloaded by the child class.

	   Returns:
	       String - html
	*/
	abstract protected function _edit();

	/*
		Function: getSearchData
			Get elements search data.

		Returns:
			String - Search data
	*/
	public function getSearchData() {
		$result = array();
		foreach ($this as $self) {
			$result[] = $this->_getSearchData();
		}

		return (empty($result) ? null : implode("\n", $result));
	}

	/*
		Function: _getSearchData
			Get repeatable elements search data.

		Returns:
			String - Search data
	*/
	protected function _getSearchData() {
		return null;
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
		foreach ($this as $self) {
			if ($this->_hasValue($params)) {
				return true;
			}
		}
		return false;
	}

	/*
		Function: _hasValue
			Checks if the repeatables element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	protected function _hasValue($params = array()) {
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

		$params = $this->app->data->create($params);
		$result = array();
		foreach ($this as $self) {
			$result[] = $this->_render($params);
		}

		return $this->app->element->applySeparators($params->get('separated_by'), $result);
	}

	/*
		Function: render
			Renders the repeatable element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	protected function _render($params = array()) {

		// render layout
		if ($layout = $this->getLayout()) {
			return $this->renderLayout($layout, array('value' => $this->get('value', $this->config->get('default'))));
		}

		return $this->get('value', $this->config->get('default'));
	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		if ($this->config->get('repeatable')) {
			$this->app->document->addScript('elements:repeatable/repeatable.js');
		}
		return $this;
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
        $this->loadAssets();
        return $this->_renderRepeatable('_renderSubmission', $params);
	}

	protected function _renderSubmission($params = array()) {}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {
		$result = array();
		foreach ($value as $single_value) {
			try {

				$result[] = $this->_validateSubmission($this->app->data->create($single_value), $params);

			} catch (AppValidatorException $e) {

				if ($e->getCode() != AppValidator::ERROR_CODE_REQUIRED) {
					throw $e;
				}
			}

		}
		if ($params->get('required') && !count($result)) {
			if (isset($e)) {
				throw $e;
			}
			throw new AppValidatorException('This field is required');
		}
		return $result;
	}

	/*
		Function: _validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function _validateSubmission($value, $params) {
		return array('value' => $this->app->validator->create('textfilter', array('required' => $params->get('required')))->clean($value->get('value')));
	}

	/*
		Function: _renderRepeatable
			Renders the repeatable

		Returns:
			String - output
	*/
    protected function _renderRepeatable($function, $params = array()) {
		return $this->config->get('repeatable') ? $this->renderLayout($this->app->path->path("elements:repeatable/tmpl/edit.php"), compact('function', 'params')) : $this->$function($params);
    }

	/*
		Function: getControlName
			Gets the controle name for given name.

		Returns:
			String - the control name
	*/
	public function getControlName($name, $array = false) {
		return "elements[{$this->identifier}][{$this->index()}][{$name}]" . ($array ? "[]":"");
	}

	/* @deprecated as of version 2.5 beta */
	public function getElementData() {
		return $this->app->data->create(parent::get("{$this->_position}"));
	}

	public function current() {
		return parent::get($this->_position);
	}

	public function next() {
		++$this->_position;
	}

	public function key() {
		return $this->_position;
	}

	public function valid() {
		if ($this->_position == 0 && !(isset($this->_item->elements[$this->identifier], $this->_item->elements[$this->identifier][0]))) {
			parent::set(0, array());
		}

		return parent::get($this->_position) !== null;
	}

	public function rewind() {
		$this->_position = 0;
	}

	public function index() {
		return $this->key();
	}

	public function count() {
		if (isset($this->_item, $this->_item->elements[$this->identifier])) {
			return count($this->_item->elements[$this->identifier]);
		}
		return 0;
	}

    public function seek($position) {
      $this->_position = $position;

      if (!$this->valid()) {
          return null;
      }
    }

}

// Declare the interface 'iRepeatSubmittable'
interface iRepeatSubmittable extends iSubmittable {

	/*
		Function: _renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
    public function _renderSubmission($params = array());

	/*
		Function: _validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
    public function _validateSubmission($value, $params);
}