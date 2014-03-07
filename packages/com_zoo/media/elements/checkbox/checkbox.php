<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register ElementOption class
App::getInstance('zoo')->loader->register('ElementOption', 'elements:option/option.php');

/*
	Class: ElementCheckbox
		The checkbox element class
*/
class ElementCheckbox extends ElementOption {

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit(){

		// init vars
		$options_from_config = $this->config->get('option', array());
		$default			 = $this->config->get('default');

		if (count($options_from_config)) {

			// set default, if item is new
			if ($default != '' && $this->_item != null && $this->_item->id == 0) {
				$default = array($default);
			} else {
				$default = array();
			}

			$selected_options  = $this->get('option', $default);

			$i       = 0;
			$html    = array('<div>');
				foreach ($options_from_config as $option) {
					$name = $this->getControlName('option', true);
					$checked = in_array($option['value'], $selected_options) ? ' checked="checked"' : null;
					$html[]  = '<div><input id="'.$name.$i.'" type="checkbox" name="'.$name.'" value="'.$option['value'].'"'.$checked.' /><label for="'.$name.$i++.'">'.$option['name'].'</label></div>';
					}
				// workaround: if nothing is selected, the element is still being transfered
				$html[] = '<input type="hidden" name="'.$this->getControlName('check').'" value="1" />';
			$html[] = '</div>';

			return implode("\n", $html);
		}

		return JText::_("There are no options to choose from.");
	}

}