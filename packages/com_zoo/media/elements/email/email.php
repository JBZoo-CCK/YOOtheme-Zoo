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
   Class: ElementEmail
       The email element class
*/
class ElementEmail extends ElementRepeatable implements iRepeatSubmittable {

	/*
		Function: _hasValue
			Checks if the repeatables element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	protected function _hasValue($params = array()) {
		$value = $this->get('value');
		return $this->_containsEmail($value);
	}

	/*
		Function: getText
			Gets the email text.

		Returns:
			String - text
	*/
	public function getText() {
		$text = $this->get('text', '');
		return empty($text) ? $this->get('value', '') : $text;
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

		// init vars
		$mode 		= $this->_containsEmail($this->getText());
		$subject	= $this->get('subject', '');
		$subject 	= !empty($subject) ? 'subject=' . $subject : '';
		$body		= $this->get('body', '');
		$body 		= !empty($body) ? 'body=' . $body : '';
		$mailto 	= $this->get('value', '');

		if ($subject && $body) {
			$mailto	.= '?' . $subject . '&' . $body;
		} elseif ($subject || $body) {
			$mailto	.= '?' . $subject . $body;
		}

		return ltrim($this->app->html->_('email.cloak', $mailto, true, $this->getText(), $mode));

	}

	/*
	   Function: _edit
	       Renders the repeatable edit form field.

	   Returns:
	       String - html
	*/
	protected function _edit(){
		return $this->_editForm();
	}

	/*
	   Function: _containsEmail
	       Checks for an email address in a text.

	   Returns:
	       Boolean - true if text contains email address, else false
	*/
	protected function _containsEmail($text) {
		return preg_match('/[\w!#$%&\'*+\/=?`{|}~^-]+(?:\.[!#$%&\'*+\/=?`{|}~^-]+)*@(?:[A-Z0-9-]+\.)+[A-Z]{2,6}/i', $text);
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
		return $this->_editForm($params->get('trusted_mode'));
	}

	protected function _editForm($trusted_mode = true) {
        if ($layout = $this->getLayout('edit.php')) {
            return $this->renderLayout($layout,
                array('trusted_mode' => $trusted_mode
                )
            );
        }
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
        $values    = $value;

        $validator = $this->app->validator->create('string', array('required' => false));
        $text      = $validator->clean($values->get('text'));
        $subject   = $validator->clean($values->get('subject'));
        $body      = $validator->clean($values->get('body'));

        $value     = $this->app->validator
				->create('email', array('required' => $params->get('required')), array('required' => 'Please enter an email address.'))
				->clean($values->get('value'));

		return compact('value', 'text', 'subject', 'body');
    }


}