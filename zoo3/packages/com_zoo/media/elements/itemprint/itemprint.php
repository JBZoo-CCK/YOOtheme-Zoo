<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementItemPrint
		The item print element class
*/
class ElementItemPrint extends Element {

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

		// include assets css
		$this->app->document->addStylesheet('elements:itemprint/assets/css/itemprint.css');

		if ($this->app->request->getBool('print', 0)) {

			// Hide respond form from the printing view
			$this->app->document->addStyleDeclaration('#comments #respond { display:none; }');

			// Hide comments if requested
			if (!$params->get('showcomments', true)) {
				$this->app->document->addStyleDeclaration('#comments { display:none; }');
			}

			return '<a class="element-print-button" onclick="window.print(); return false;" href="#"></a>';

		} else {

			$this->app->html->_('behavior.modal', 'a.modal-button');
			$text  = $params->get('showicon') ? '' : JText::_('Print');
			$class = $params->get('showicon') ? 'modal-button element-print-button' : 'modal-button';

			return '<a href="'.JRoute::_($this->app->route->item($this->_item, false).'&amp;tmpl=component&amp;print=1').'" title="'.JText::_('Print').'" rel="{handler: \'iframe\', size: {x: 850, y: 500}}" class="'.$class.'">'.$text.'</a>';

		}
	}

}