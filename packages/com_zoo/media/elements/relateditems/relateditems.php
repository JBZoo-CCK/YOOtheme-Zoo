<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementRelatedItems
		The related items element class
*/
class ElementRelatedItems extends Element implements iSubmittable {

	protected $_related_items;

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$items = $this->_getRelatedItems();
		return !empty($items);
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

		// init vars
		$params   = $this->app->data->create($params);
		$renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->_item->getApplication()->getTemplate()->getPath()));
		$items    = $this->_orderItems($this->_getRelatedItems($params), $params->get('order'));

		// create output
		$layout   = $params->get('layout');
		$output   = array();
		foreach ($items as $item) {
			$path   = 'item';
			$prefix = 'item.';
			$type   = $item->getType()->id;
			if ($renderer->pathExists($path.DIRECTORY_SEPARATOR.$type)) {
				$path   .= DIRECTORY_SEPARATOR.$type;
				$prefix .= $type.'.';
			}

			if (in_array($layout, $renderer->getLayouts($path))) {
				$output[] = $renderer->render($prefix.$layout, array('item' => $item));
			} elseif ($params->get('link_to_item', false) && $item->getState()) {
				$output[] = '<a href="'.$this->app->route->item($item).'" title="'.$item->name.'">'.$item->name.'</a>';
			} else {
				$output[] = $item->name;
			}
		}

		return $this->app->element->applySeparators($params->get('separated_by'), $output);
	}

	protected function _orderItems($items, $order) {

		// if string, try to convert ordering
		if (is_string($order)) {
			$order = $this->app->itemorder->convert($order);
		}

		$items = (array) $items;
		$order = (array) $order;
		$sorted = array();
		$reversed = false;

		// remove empty values
		$order = array_filter($order);

		// if random return immediately
		if (in_array('_random', $order)) {
			shuffle($items);
			return $items;
		}

		// get order dir
		if (($index = array_search('_reversed', $order)) !== false) {
			$reversed = true;
			unset($order[$index]);
		} else {
			$reversed = false;
		}

		// order by default
		if (empty($order)) {
			return $reversed ? array_reverse($items, true) : $items;
		}

		// if there is a none core element present, ordering will only take place for those elements
		if (count($order) > 1) {
			$order = array_filter($order, create_function('$a', 'return strpos($a, "_item") === false;'));
		}

		if (!empty($order)) {

			// get sorting values
			foreach ($items as $item) {
				foreach ($order as $identifier) {
					if ($element = $item->getElement($identifier)) {
						$sorted[$item->id] = strpos($identifier, '_item') === 0 ? $item->{str_replace('_item', '', $identifier)} : $element->getSearchData();
						break;
					}
				}
			}

			// do the actual sorting
			$reversed ? arsort($sorted) : asort($sorted);

			// fill the result array
			foreach (array_keys($sorted) as $id) {
				if (isset($items[$id])) {
					$sorted[$id] = $items[$id];
				}
			}

			// attach unsorted items
			$sorted += array_diff_key($items, $sorted);

		// no sort order provided
		} else {
			$sorted = $items;
		}

		return $sorted;
	}

	protected function _getRelatedItems($published = true) {

		if ($this->_related_items == null) {

			// init vars
			$table = $this->app->table->item;
			$this->_related_items = array();
			$related_items = array();

			// get items
			$items = $this->get('item', array());

			// check if items have already been retrieved
			foreach ($items as $key => $id) {
				if ($table->has($id)) {
					$related_items[$id] = $table->get($id);
					unset($items[$key]);
				}
			}

			if (!empty($items)) {
				// get dates
				$db   = $this->app->database;
				$date = $this->app->date->create();
				$now  = $db->Quote($date->toSQL());
				$null = $db->Quote($db->getNullDate());
				$items_string = implode(', ', $items);
				$conditions = $table->key.' IN ('.$items_string.')'
							. ($published ? ' AND state = 1'
							.' AND '.$this->app->user->getDBAccessString()
							.' AND (publish_up = '.$null.' OR publish_up <= '.$now.')'
							.' AND (publish_down = '.$null.' OR publish_down >= '.$now.')' : '');
				$order = 'FIELD('.$table->key.','.$items_string.')';
				$related_items += $table->all(compact('conditions', 'order'));
			}

			foreach ($this->get('item', array()) as $id) {
				if (isset($related_items[$id])) {
					$this->_related_items[$id] = $related_items[$id];
				}
			}

		}

		return $this->_related_items;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->_edit(false);
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

		// load assets
		$this->app->document->addScript('elements:relateditems/relateditems.js');

		return $this->_edit();

	}

	protected function _edit($published = true) {

		$query = array('controller' => 'item', 'task' => 'element', 'tmpl' => 'component', 'func' => 'selectRelateditem', 'object' => $this->identifier);

		// filter types
		foreach ($this->config->get('selectable_types', array()) as $key => $selectable_type) {
			$query["type_filter[$key]"] = $selectable_type;
		}

		// filter items
		if ($this->_item) {
			$query['item_filter'] = $this->_item->id;
		}

		if ($layout = $this->getLayout('edit.php')) {
            return $this->renderLayout($layout,
                array(
                    'data' => $this->_getRelatedItems($published),
					'link' => $this->app->link($query)
                )
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

        $options     = array('required' => $params->get('required'));
		$messages    = array('required' => 'Please select at least one related item.');

        $items = (array) $this->app->validator
				->create('foreach', $this->app->validator->create('integer'), $options, $messages)
				->clean($value->get('item'));

		$table = $this->app->table->item;
        if ($selectable_types = $this->config->get('selectable_types', array()) and !empty($selectable_types)) {
			foreach ($items as $item) {
				if (!empty($item) && !in_array($table->get($item)->type, $selectable_types)) {
					throw new AppValidatorException('Please choose a correct related item.');
				}
			}
		}

		return array('item' => $items);
	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		$this->app->document->addScript('elements:relateditems/relateditems.js');
	}

	/*
		Function: getConfigForm
			Get parameter form object to render input form.

		Returns:
			Parameter Object
	*/
	public function getConfigForm() {
		return parent::getConfigForm()->addElementPath(dirname(__FILE__));
	}

}