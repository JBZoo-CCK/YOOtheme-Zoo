<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register ElementRepeatable class
App::getInstance('zoo')->loader->register('ElementRepeatable', 'elements:repeatable/repeatable.php');

/*
   Class: ElementDate
   The date element class
*/
class ElementDate extends ElementRepeatable implements iRepeatSubmittable {

	const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M:%S';

	/*
		Function: _getSearchData
			Get repeatable elements search data.

		Returns:
			String - Search data
	*/
	protected function _getSearchData() {
		return $this->get('value');
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
		$params = $this->app->data->create($params);
		return $this->app->html->_('date', $this->get('value', ''), $this->app->date->format($params->get('date_format') == 'custom' ? $params->get('custom_format') : $params->get('date_format')));
	}

	/*
	   Function: _edit
	       Renders the repeatable edit form field.

	   Returns:
	       String - html
	*/
	protected function _edit() {
		$name = $this->getControlName('value');
		if ($value = $this->get('value', '')) {

			try {

				$value = $this->app->html->_('date', $value, $this->app->date->format(self::EDIT_DATE_FORMAT), $this->app->date->getOffset());

			} catch (Exception $e) {}

		}
		return $this->app->html->_('zoo.calendar', $value, $name, $name, array('class' => 'calendar-element'), true);
	}

	/*
		Function: _renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function _renderSubmission($params = array()) {
		return $this->_edit();
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

		$value = $value->get('value');
		if (!empty($value) && ($time = strtotime($value))) {
			$value = strftime(self::EDIT_DATE_FORMAT, $time);
		}

        return array('value' => $this->app->validator->create('date', array('required' => $params->get('required')), array('required' => 'Please choose a date.'))
				->addOption('date_format', self::EDIT_DATE_FORMAT)
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
		parent::bindData($data);
		foreach ($this as $self) {
			$value = $this->get('value', '');
			if (!empty($value) && ($value = strtotime($value)) && ($value = strftime(self::EDIT_DATE_FORMAT, $value))) {
				$tzoffset = $this->app->date->getOffset();
				$date     = $this->app->date->create($value, $tzoffset);
				$value	  = $date->toSQL();
				$this->set('value', $value);
			}
		}
	}

}