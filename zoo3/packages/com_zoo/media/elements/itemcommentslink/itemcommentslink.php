<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementItemCommentsLink
		The item comments link element class
*/
class ElementItemCommentsLink extends Element {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		return $this->_item && $this->_item->getApplication()->isCommentsEnabled() && ($this->_item->isCommentsEnabled() || $this->_item->getCommentsCount());
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
			$comment_count = $this->_item->getCommentsCount();

			if ($comment_count == 0) {
				$text = $params->get('no_comments_text', JText::_('No comments'));
			} else if ($comment_count == 1) {
				$text = sprintf($params->get('single_comment_text', JText::_('%s comment')), 1);
			} else {
				$text = sprintf($params->get('multiple_comments_text', JText::_('%s comments')), $comment_count);
			}

            if ($this->_item->getState()) {

                return '<a href="' . $this->app->route->item($this->_item).'#comments">' . $text . '</a>';

            } else {

                return $text;

            }

		}

	}

}