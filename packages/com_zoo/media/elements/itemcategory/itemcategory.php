<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: ElementItemCategory
       The item category element class
*/
class ElementItemCategory extends Element implements iSubmittable {

	protected $_categories;

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$categories = $this->_item->getRelatedCategories(true);
		return !empty($categories);
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
		$values = array();
		foreach ($this->_item->getRelatedCategories(true) as $category) {
			$values[] = $params->get('linked') ? '<a href="'.$this->app->route->category($category).'">'.$category->name.'</a>' : $category->name;
		}

		return $this->app->element->applySeparators($params->get('separated_by'), $values);
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
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
		if ($layout = $this->getLayout('submission.php')) {
            return $this->renderLayout($layout, compact('params'));
        }
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
		$primary = (int) $value->get('primary');
		$value = (array) $value->get('value');

		if (!$params->get('multiple', true) && count($value) > 1) {
			$value = array(array_shift($value));
		}

		$primary = !$params->get('primary', false) || empty($primary) || !in_array($primary, $value) ? @$value[0] : $primary;

		$categories = array_keys($this->_item->getApplication()->getCategories());
		foreach ($value as $id) {
			if (!in_array($id, $categories)) {
				throw new AppValidatorException('Please choose valid categories only');
			}
		}

        if ($params->get('required') && !count($value)) {
            throw new AppValidatorException('Please choose a category');
        }

		// connect to submission aftersave event
		$this->app->event->dispatcher->connect('submission:saved', array($this, 'aftersubmissionsave'));

        return compact('value', 'primary');
	}

	/*
		Function: afterSubmissionSave
			Callback after item submission is saved

		Returns:
			void
	*/
	public function afterSubmissionSave() {
		if (!empty($this->_categories)) {
			if (in_array('0', $this->app->category->getItemsRelatedCategoryIds($this->_item->id))) {
				$this->_categories[] = 0;
			}
			$this->app->category->saveCategoryItemRelations($this->_item, $this->_categories);
		}
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
		$this->_categories = @$data['value'];
		$this->_item->getParams()->set('config.primary_category', @$data['primary']);
	}

}