<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: ElementAddthis
       The Addthis element class (http://www.addthis.com)
*/
class ElementAddthis extends Element implements iSubmittable {

	/*
		Function: render
			Override. Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// render html
		if (($account = $this->config->get('account')) && $this->get('value', $this->config->get('default'))) {
			$html[] = "<a class=\"addthis_button\" href=\"http://www.addthis.com/bookmark.php?v=250&amp;username=$account\">";
			$html[] = "<img src=\"http://s7.addthis.com/static/btn/v2/lg-share-en.gif\" width=\"125\" height=\"16\" alt=\"Bookmark and Share\" style=\"border:0\"/>";
			$html[] = "</a>";
			$html[] = "<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#username=$account\"></script>";
			return implode("\n", $html);
		}

		return '';
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->app->html->_('select.booleanlist', $this->getControlName('value'), '', $this->get('value', $this->config->get('default')));
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
		return array('value' => (bool) $value->get('value'));
	}

}