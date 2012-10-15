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

		$tags = isset($this->_tags) ? $this->_tags : $this->_item->getTags();

		$html[] = '<div id="tag-area">';
		$html[] = '<input type="text" value="'.implode(', ', $tags).'" placeholder="'.JText::_('Add tag').'" />';
		$html[] = '<p>'.JText::_('Choose from the most used tags').':</p>';
		$most = $this->app->table->tag->getAll($this->_item->getApplication()->id, null, null, 'items DESC, a.name ASC', null, 8);
		if (count($most)) {
			$html[] = '<div class="tag-cloud">';
				foreach ($most as $tag) {
					$html[] = '<a title="'.$tag->items . ' ' . ($tag->items == 1 ? JText::_('item') : JText::_('items')).'">'.$tag->name.'</a>';
				}
			$html[] = '</div>';
		}
		$html[] = '</div>';

		// init vars
		$link = $this->app->link(array('controller' => 'submission', 'task' => 'loadtags', 'format' => 'raw'), false);
		$this->app->document->addScriptDeclaration("jQuery(function($) { $('#tag-area').Tag({url: '".$link."', inputName: '".$this->getControlName('value', true)."'}); });");

		return implode("\n", $html);
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
		return (array) $value;
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