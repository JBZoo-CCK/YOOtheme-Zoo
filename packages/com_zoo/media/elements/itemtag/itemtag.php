<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementItemTag
		The item tag element class
*/
class ElementItemTag extends Element implements iSubmittable{

	protected $_tags;

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();

		// set callbacks
		$this->registerCallback('tags');
	}

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$tags = $this->_item->getTags();
		return !empty($tags);
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
		if ($params->get('linked')) {
			foreach ($this->_item->getTags() as $tag) {
				$values[] = '<a href="'.JRoute::_($this->app->route->tag($this->_item->application_id, $tag)).'">'.$tag.'</a>';
			}
		} else {
			$values = $this->_item->getTags();
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
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		$this->app->document->addScript('assets:js/autosuggest.js');
		$this->app->document->addScript('assets:js/tag.js');
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

		// init vars
        $tags = isset($this->_tags) ? $this->_tags : $this->_item->getTags();
		$most = $this->app->table->tag->getAll($this->_item->getApplication()->id, null, null, 'items DESC, a.name ASC', null, 8);
		$link = $this->app->link(array('controller' => 'submission', 'task' => 'loadtags', 'format' => 'raw'), false);

        if ($layout = $this->getLayout('submission.php')) {
            return $this->renderLayout($layout,
                compact('tags', 'most', 'link')
            );
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
		$values = (array) $value;
		foreach ($values as $value) {
			$value = $this->app->validator->create('textfilter')->clean($value);
		}

		return $values;
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
		$this->_item->setTags((array) @$data['value']);
	}

}