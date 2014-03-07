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
   Class: ElementTextarea
   The textarea element class
*/
class ElementTextarea extends ElementRepeatable implements iSubmittable {

	const ROWS = 20;
	const COLS = 60;
	const MAX_HIDDEN_EDITORS = 5;

	/*
		Function: bindData
			Set data through data array.

		Parameters:
			$data - array

		Returns:
			Void
	*/
	public function bindData($data = array()) {

		// set raw input for textarea
		$post = $this->app->request->get('post', JREQUEST_ALLOWRAW);
		foreach ($data as $index => $instance_data) {
			if (isset($post['elements'][$this->identifier][$index]['value'])) {
				$data[$index]['value'] = $post['elements'][$this->identifier][$index]['value'];
			}
		}

		parent::bindData($data);

	}

	/*
		Function: _getSearchData
			Get repeatable elements search data.

		Returns:
			String - Search data
	*/
	protected function _getSearchData() {

		// clean html tags
		$value  = JFilterInput::getInstance()->clean($this->get('value', ''));

		return (empty($value) ? null : $value);
	}

	/*
		Function: hasValue
			Override. Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$params = $this->app->data->create($params);
		switch ($params->get('display', 'all')) {
			case 'all':
				foreach ($this as $self) {
					if ($this->_hasValue($params)) {
						return true;
					}
				}
				break;
			case 'first':
				$this->seek(0);
				return $this->_hasValue($params);
				break;
			case 'all_without_first':
				$this->seek(1);
				while ($this->valid()) {
					if ($this->_hasValue($params)) {
						return true;
					}
					$this->next();
				}
				break;
		}

		return false;
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

		$params   = $this->app->data->create($params);
		$jplugins = $this->config->get('jplugins');

		$result = array();
		switch ($params->get('display', 'all')) {
			case 'all':
				foreach ($this as $self) {
					if (($text = $this->get('value', '')) && !empty($text)) {
						$result[] = $text;
					}
				}
				break;
			case 'first':
				$this->seek(0);
				if (($text = $this->get('value', '')) && !empty($text)) {
					$result[] = $text;
				}
				break;
			case 'all_without_first':
				$this->seek(1);
				while ($this->valid()) {
					if (($text = $this->get('value', '')) && !empty($text)) {
						$result[] = $text;
					}
					$this->next();
				}
				break;
		}

		// trigger joomla content plugins
		if ($jplugins) {
			for ($i = 0; $i < count($result); $i++) {
				$result[$i] = $this->app->zoo->triggerContentPlugins($result[$i], array('item_id' => $this->_item->id), 'com_zoo.element.textarea');
			}
		}

		return $this->app->element->applySeparators($params->get('separated_by'), $result);
	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		parent::loadAssets();
		if ($this->config->get('repeatable')) {
			$this->app->document->addScript('elements:textarea/textarea.js');
		}
		return $this;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->_edit(array('trusted_mode' => true));
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
        $this->loadAssets();
        return $this->_edit($params);
	}

    protected function _edit($params = array()) {

		$params = $this->app->data->create($params);

		$this->rewind();

		return $this->config->get('repeatable') ? $this->renderLayout($this->getLayout('edit.php'), compact('params')) : $this->_addEditor(0, $this->get('value', $this->config->get('default')), $params->get('trusted_mode', false));

	}

	protected function _addEditor($index, $value = '', $trusted_mode = true) {
		$html[] = '<div class="repeatable-content">';
		if ($trusted_mode) {
			$html[] = $this->app->editor->display($this->getControlName('value', false, $index), htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), null, null, self::COLS, self::ROWS, array('pagebreak', 'readmore', 'article'));
        } else {
			$html[] = $this->app->html->_('control.textarea', $this->getControlName('value', false, $index), $value, 'cols='.self::COLS.' rows='.self::ROWS);
		}
		$html[] = '</div>';
		$html[] = '<span class="delete" title="'.JText::_('Delete Element').'"></span>';
		return implode("\n", $html);
	}

	public function getControlName($name, $array = false, $index = 0) {
		return "elements[{$this->identifier}][{$index}][$name]";
	}

}