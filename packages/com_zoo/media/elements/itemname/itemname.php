<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementItemName
		The item name element class
*/
class ElementItemName extends Element implements iSubmittable {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		return true;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return null;
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
		if (!empty($this->_item)) {

			$params = $this->app->data->create($params);

			if ($params->get('link_to_item', false) && $this->_item->getState()) {

				return '<a title="'.$this->_item->name.'" href="' . $this->app->route->item($this->_item) . '">' . $this->_item->name . '</a>';

			} else {

				return $this->_item->name;

			}
		}
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
			$value  - AppData value
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
       return '<input type="text" name="'.$this->getControlName('value').'" size="60" value="'.$this->_item->name.'" />';
	}

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
		return array('value' => $this->app->validator->create('textfilter', array('required' => $params->get('required')))->clean($value->get('value')));
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
		$this->_item->name = @$data['value'];
	}

}