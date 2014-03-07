<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: ElementJoomlamodule
       The Joomla module wapper element class
*/
class ElementJoomlamodule extends Element implements iSubmittable {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		// get modules
		$modules = $this->app->module->load(true);
		$value   = $this->get('value', $this->config->get('default'));

		if ($value && isset($modules[$value])) {
			return true;
		}

		return false;
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

		// get modules
		$modules = $this->app->module->load(true);
		$value   = $this->get('value', $this->config->get('default'));

		if ($value && isset($modules[$value])) {
			$rendered = JModuleHelper::renderModule($modules[$value]);

			if (isset($modules[$value]->params)) {
				$module_params = $this->app->parameter->create($modules[$value]->params);
				if ($moduleclass_sfx = $module_params->get('moduleclass_sfx')) {
					$html[] = '<div class="'.$moduleclass_sfx.'">';
					$html[] = $rendered;
					$html[] = '</div>';

					return implode("\n", $html);
				}
			}

			return $rendered;
		}

		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		// init vars
		$options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Module').' -'));

		return $this->app->html->_('zoo.modulelist', $options, $this->getControlName('value'), null, 'value', 'text', $this->get('value', $this->config->get('default')));

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
        return $this->edit();
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
		return array('value' => $value->get('value'));
	}

}