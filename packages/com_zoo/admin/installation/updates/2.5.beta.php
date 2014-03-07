<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class Update25BETA implements iUpdate {

	protected $_repeatable_elements = array();

    /*
		Function: getNotifications
			Get preupdate notifications.

		Returns:
			Array - messages
	*/
	public function getNotifications($app) {

		$app->error->raiseNotice(0, JText::_('Once you upgrade you\'ll not be able to downgrade to an earlier ZOO version. If you don\'t want to run this update, simply reinstall your previous ZOO version now.'));

		$msg = array();
		$msg[] = JText::_('All ZOOtools will be removed by this update.');
		$msg[] = JText::_('For the Gallery element you\'ll need to install YOOthemes Widgetkit! The old Gallery element will be removed by this update.');
		$msg[] = JText::_('For the image lightbox and spotlight options you\'ll need to install YOOthemes Widgetkit.');
		$msg[] = JText::_('There is a new Social Buttons element - it replaces the Facebook I Like element.');
		$msg[] = JText::_('There is a new Media element - it will replace the Video element. The data will be converted where applicable.');
		$msg[] = JText::_('Custom elements should be updated, some need to be adapted to the new elements behavior for the ZOO to function correctly.');
		return $msg;
	}

    /*
		Function: run
			Performs the update.

		Returns:
			bool - true if updated successful
	*/
	public function run($app) {

		$json = $app->data->create();
		$applications = $app->application->groups();
		foreach ($applications as $application) {

			// convert xml types to json
			$files = $app->path->files($application->getResource() . 'types', false, '/\.xml$/');
			foreach ($files as $file) {
				$path = $app->path->path($application->getResource() . 'types/').'/';
				if ($xml_data = $app->data->create(json_encode(simplexml_load_file($path.$file)))) {
					$data = array();
					$data['name'] = $xml_data['@attributes']['name'];
					foreach ($xml_data['params']['param'] as $param) {
						$element = array();
						foreach ($param as $name => $option) {
							if ($name === '@attributes') {
								$element += $option;
							} else {
								$element[$name] = array();
								foreach ($option as $key => $value) {
									if ($key === '@attributes') {
										if (count($value) > 1) {
											$element[$name][] = $value;
										} else {
											$element[$name][] = array_pop($value);
										}
									} else {
										foreach ($value as $child) {
											if (count($child) > 1) {
												$element[$name][] = $child;
											} else {
												$element[$name][] = array_pop($child);
											}
										}
									}
								}
							}
						}
						$identifier = $element['identifier'];
						unset($element['identifier']);
						$data['elements'][$identifier] = $element;
					}

					// write new type config file
					JFile::write($path.basename($file, '.xml').'.config', $json->_jsonEncode($data));

					// remove old type xml file
					JFile::delete($path.$file);
				}

			}

			// change type of video element to media element
			foreach ($application->getTypes() as $type) {
				$changed = false;
				$elements = $type->elements;
				foreach ($elements as $key => $element) {
					if (isset($element['type']) && $element['type'] == 'video') {
						$elements[$key]['type'] = 'media';
						$changed = true;
					}
				}
				$type->elements = $elements;
				if ($changed) {
					$type->save();
				}
			}
		}

		// remove obsolete elements
		foreach (array('video', 'gallery', 'facebookilike') as $element) {
			if ($folder = $app->path->path('media:zoo/elements/'.$element)) {
				JFolder::delete($folder);
			}
		}

		// convert xml element data to JSON data
		$elements_array = array('relateditems', 'country', 'select', 'checkbox', 'radio', 'relatedcategories');
		$items = $app->database->queryObjectList('SELECT id, application_id, elements, type FROM '.ZOO_TABLE_ITEM);
		foreach ($items as $item) {
			$application = $app->table->application->get($item->application_id);
			if ($xml_data = simplexml_load_string($item->elements)) {
				$element_data = array();
				foreach ($xml_data as $xml_element) {
					if ($element_type = $xml_element->getName()) {
						$identifier = (string) $xml_element->attributes()->identifier;
						$data = array();
						foreach ($xml_element->children() as $child) {
							$name = $child->getName();
							if ($element_type == 'video' && $name == 'file' && (string) $child) {
								$data[$name] = $application->getType($item->type)->getElementConfig($identifier)->get('directory') . '/' . (string) $child;
							} else if (in_array($element_type, $elements_array)) {
								$data[$name][] = (string) $child;
							} else {
								$data[$name] = (string) $child;
							}
						}
						if ($this->_isRepeatable($app, $element_type)) {
							$element_data[$identifier][] = $data;
						} else {
							$element_data[$identifier] = $data;
						}
					}
				}

				$query = "UPDATE ".ZOO_TABLE_ITEM." SET elements = ".$app->database->quote($json->_jsonEncode($element_data))." WHERE id = ".$item->id;
				$app->database->query($query);
			}
		}

		// sanatize item order
		foreach ($app->table->application->all() as $application) {

			try {

				$item_order = $application->getParams()->get('global.config.item_order');
				if (is_string($item_order)) {
				$application->getParams()->set('global.config.item_order', $app->itemorder->convert($item_order));
				$app->table->application->save($application);
				}

				$item_order = $application->getParams()->get('config.item_order');
				if (is_string($item_order)) {
					$application->getParams()->set('config.item_order', $app->itemorder->convert($item_order));
					$app->table->application->save($application);
				}

				foreach ($application->getCategories() as $category) {
					$item_order = $category->getParams()->get('config.item_order');
					if (is_string($item_order)) {
						$category->getParams()->set('config.item_order', $app->itemorder->convert($item_order));
						$app->table->category->save($category);
					}
				}
			} catch (AppException $e) {}

		}

		// remove zoo tools
		jimport('joomla.installer.installer');
		$uninstaller = new JInstaller();
		if ($ids = $app->database->queryResultArray('SELECT extension_id as id FROM #__extensions WHERE element in ("mod_zooaccordion", "mod_zoocarousel", "mod_zoodrawer", "mod_zoomaps", "mod_zooscroller", "mod_zooslider")') and is_array($ids)) {
			foreach ($ids as $id) {
				$uninstaller->uninstall('module', $id, 0);
			}
		}

	}

	protected function _isRepeatable($app, $type) {
		if (isset($this->_repeatable_elements[$type])) {
			return $this->_repeatable_elements[$type];
		}

		if ($file = $app->path->path("elements:$type/$type.php")) {
			if ($content = file_get_contents($file)) {
				$this->_repeatable_elements[$type] = preg_match('/class(.*)extends(.*)ElementRepeatable/i', $content);
				return $this->_repeatable_elements[$type];
			}
		}

		return null;
	}

}