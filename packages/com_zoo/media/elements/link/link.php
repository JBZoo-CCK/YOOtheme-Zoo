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
   Class: ElementLink
       The link element class
*/
class ElementLink extends ElementRepeatable implements iRepeatSubmittable {

	/*
		Function: _hasValue
			Checks if the repeatables element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	protected function _hasValue($params = array()) {
		$link = $this->get('value', '');
		return !empty($link);
	}

	/*
		Function: getText
			Gets the link text.

		Returns:
			String - text
	*/
	public function getText() {
		$text = $this->get('text', '');
		return empty($text) ? $this->get('value', '') : $text;
	}

	/*
		Function: getTitle
			Gets the link title.

		Returns:
			String - title
	*/
	public function getTitle() {
		$title = $this->get('custom_title', '');
		return empty($title) ? $this->getText() : $title;
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

		$target = ($this->get('target', $this->config->get('default_target', ''))) ? 'target="_blank"' : '';
		$rel	= $this->get('rel', '');
		
		// render layout
		if ($layout = $this->getLayout()) {
			return $this->renderLayout($layout,
				compact('target', 'rel')
			);
		}

		return null;

	}

	/*
	   Function: _edit
	       Renders the repeatable edit form field.

	   Returns:
	       String - html
	*/
	protected function _edit() {
		return $this->_editForm();
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
                compact('trusted_mode')
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
        $values       = $value;

        $validator    = $this->app->validator->create('string', array('required' => false));
        $text         = $validator->clean($values->get('text'));
        $target       = $validator->clean($values->get('target', $this->config->get('default_target', '')));
        $custom_title = $validator->clean($values->get('custom_title'));
        $rel          = $validator->clean($values->get('rel'));

        $value        = $this->app->validator
				->create('url', array('required' => $params->get('required')), array('required' => 'Please enter an URL.'))
				->clean($values->get('value'));

		return compact('value', 'text', 'target', 'custom_title', 'rel');
    }

}