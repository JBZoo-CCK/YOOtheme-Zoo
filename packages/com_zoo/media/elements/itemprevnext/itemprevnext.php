<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementItemPrevNext
		The item prev next element class
*/
class ElementItemPrevNext extends Element {

	protected $_items = null;

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		@list($prev, $next) = $this->_getAdjacentItems();
		return $prev || $next;
	}

	protected function _getAdjacentItems() {
		if ($this->_items === null) {

			// get category_id
			if (!$category_id = $this->app->request->getInt('category_id')) {
				$category_id = (int) $this->_item->getPrimaryCategoryId();
			}

			if ($category = $this->app->table->category->get((int) $category_id)) {
				$order = $category->getParams('site')->get('config.item_order');
			} else {
				$order = $this->_item->getApplication()->getParams('frontpage')->get('config.item_order');
			}

			$this->_items = $this->app->table->item->getPrevNext($this->_item->getApplication()->id, (int) $category_id, $this->_item->id, true, null, $order);

		}

		return $this->_items;
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

		@list($prev, $next) = $this->_getAdjacentItems();

		$category_id = $this->app->request->getInt('category_id');
		$prev_link = $prev ? JRoute::_($this->app->route->item($prev, false).($category_id ? '&amp;category_id='.$category_id : '')) : '';
		$next_link = $next ? JRoute::_($this->app->route->item($next, false).($category_id ? '&amp;category_id='.$category_id : '')) : '';

		// render layout
		if ($layout = $this->getLayout()) {
			return $this->renderLayout($layout, compact('prev', 'prev_link', 'next', 'next_link'));
		}
	}

}