<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementItemPublish_Down
		The item publish down element class
*/
class ElementItemPublish_Down extends Element implements iSubmittable {

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
		$params = $this->app->data->create($params);
		return $this->app->html->_('date', $this->_item->publish_down, $this->app->date->format($params->get('date_format') == 'custom' ? $params->get('custom_format') : $params->get('date_format')), $this->app->date->getOffset());
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
		$name = $this->getControlName('value');
		if ($this->_item->publish_down == $this->app->database->getNullDate()) {
			$value = JText::_('Never');
		} else if ($value = $this->_item->publish_down) {
			$value = $this->app->html->_('date', $value, $this->app->date->format(SubmissionController::EDIT_DATE_FORMAT), $this->app->date->getOffset());
		}
		return $this->app->html->_('zoo.calendar', $value, $name, $name, array('class' => 'calendar-element'), true);
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
		$value = $value->get('value');
		if ((strtolower(trim($value)) == strtolower(JText::_('Never'))) || trim($value) == '') {
			return array('value' => $this->app->database->getNullDate());
		}
        return array('value' => $this->app->validator->create('date', array('required' => $params->get('required')), array('required' => 'Please choose a date.'))
				->addOption('date_format', SubmissionController::EDIT_DATE_FORMAT)
				->clean($value));
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
		$value = @$data['value'];
		if (!empty($value) && ($value = strtotime($value)) && $value > 0 && ($value = strftime(SubmissionController::EDIT_DATE_FORMAT, $value))) {
			$tzoffset = $this->app->date->getOffset();
			$date     = $this->app->date->create($value, $tzoffset);
			$value	  = $date->toSQL();
			$this->_item->publish_down = $value;
		} else {
			$this->_item->publish_down = $this->app->database->getNullDate();
		}
	}

}