<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: ElementEdit
*/
class ElementItemEdit extends Element {


	/*
		Function: hasValue
			Override. Checks if the element's value is set.

	   Parameters:
			$params - AppData render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		// Check if the user can access the sub itself and if an item edit submission is set
		return $this->_item && ($submission = $this->_item->getApplication()->getItemEditSubmission()) && $submission->canAccess();
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - AppData render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// render layout
		if ($layout = $this->getLayout()) {

			// Get the item edit submission
			if ($submission = $this->getItem()->getApplication()->getItemEditSubmission()) {
				return $this->renderLayout($layout, array(
					'item' => $this->getItem(),
					'submission' => $submission
					)
				);
			}
		}
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
}