<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Import helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ImportHelper extends AppHelper {

	/**
	 * Import from JSON file.
	 *
	 * @param string $json_file The JSON file path
	 * @param boolean $import_frontpage if true, frontpage will be imported
	 * @param boolean $import_categories Import with categories
	 * @param array $element_assignment The element assignment array
	 * @param array $types The selected types array
	 *
	 * @return boolean true on success
	 * @throws ImportHelperException
	 *
	 * @since 2.0
	 */
	public function import($json_file, $import_frontpage = true, $import_categories = true, $element_assignment = array(), $types = array()) {

		// set_time_limit doesn't work in safe mode
		if (!ini_get('safe_mode')) {
			@set_time_limit(0);
		}

		if (!(JFile::exists($json_file) && $data = $this->app->data->create(file_get_contents($json_file)))) {
			throw new ImportHelperException('No valid json file.');
		}

		// get application
		if (!$application = $this->app->zoo->getApplication()) {
			throw new ImportHelperException('No application to import too.');
		}

		// import frontpage
		if (isset($data['categories'], $data['categories']['_root']) && $import_frontpage) {
			$this->_importFrontpage($application, $data['categories']['_root']);
		}

		// import categories
		$categories = array();
		if (isset($data['categories']) && count($data['categories']) && $import_categories) {
			$categories_to_import = $data['categories'];
			unset($categories_to_import['_root']);
			$categories = $this->_importCategories($application, $categories_to_import);
		}

		// import items
		if (isset($data['items'])) {
			$this->_importItems($application, $data['items'], $element_assignment, $types, $categories);
		}

		return true;

	}

	/**
	 * Import the frontpage settings
	 *
	 * @param Application $application The Application object
	 * @param array $frontpage the frontpage settings
	 *
	 * @since 2.0
	 */
	private function _importFrontpage(Application $application, $frontpage) {

		$application->description = $frontpage['description'];

		// set frontpage content params
		if (isset($frontpage['content'])) {
			$application->getParams()->set('content.', $frontpage['content']);
		}

		// set frontpage metadata params
		if (isset($frontpage['metadata'])) {
			$application->getParams()->set('metadata.', $frontpage['metadata']);
		}

		// save application
		try {

			$this->app->table->application->save($application);

		} catch (AppException $e) {
			$this->app->error->raiseNotice(0, JText::_('Error Importing Frontpage').' ('.$e.')');
		}
	}

	/**
	 * Import the categories
	 *
	 * @param Application $application The Application object
	 * @param array $categories the categories data
	 *
	 * @since 2.0
	 */
	private function _importCategories(Application $application, $categories = array()) {

		// init vars
		$db = $this->app->database;
		$table = $this->app->table->category;
		$category_vars = array_keys(get_class_vars('Category'));

		// first iteration: save category vars
		$category_objects = array();
		foreach ($categories as $alias => $category) {

			$category_obj = $this->app->object->create('Category');
			$category_obj->alias = $this->app->string->sluggify($alias);

			// set a valid category alias
			$category_obj->alias = $this->app->alias->category->getUniqueAlias(0, $category_obj->alias);

			// set category values
			foreach ($category as $property => $value) {
				if (in_array($property, $category_vars)) {
					$category_obj->$property = $value;
				}
			}
			$category_obj->parent = 0;
			$category_obj->application_id = $application->id;

			// set category content params
			if (isset($category['content'])) {
				$category_obj->getParams()->set('content.', $category['content']);
			}

			// set category metadata params
			if (isset($category['metadata'])) {
				$category_obj->getParams()->set('metadata.', $category['metadata']);
			}

			$db->query('INSERT INTO '.ZOO_TABLE_CATEGORY.'(alias) VALUES ('.$db->quote($category_obj->alias).')');
			$category_obj->id = $db->insertid();

			// store category for second iteration
			$category_objects[$alias] = $category_obj;
		}

		// second iteration: set parent relationship
		foreach ($categories as $alias => $category) {

			// only save if parent is set
			if (isset($category_objects[$alias])) {
				if (!empty($category['parent']) && $category['parent'] != '_root') {
					$category_objects[$alias]->parent = $category_objects[$category['parent']]->id;
				}
			}

			// save the category
			try {

				$table->save($category_objects[$alias]);

			} catch (AppException $e) {
				$this->app->error->raiseNotice(0, JText::_('Error Importing Category').' ('.$e.')');
			}
		}

		return $category_objects;
	}

	/**
	 * Imports the items
	 *
	 * @param Application $application The Application object
	 * @param array $items the items to import
	 * @param array $element_assignment The element assignment array
	 * @param array $types The selected types array
	 * @param array $categories The category objects
	 *
	 * @since 2.0
	 */
	private function _importItems(Application $application, $items = array(), $element_assignment = array(), $types = array(), $categories = array()) {

		// init vars
		$db = $this->app->database;
		$table = $this->app->table->item;
		$comment_table = $this->app->table->comment;
		$item_vars = array_keys(get_class_vars('Item'));
		$comment_vars = array_keys(get_class_vars('Comment'));
		$user_id = $this->app->user->get()->get('id');
		$app_types = $application->getTypes();
		$authors = $this->app->data->create($db->queryObjectList('SELECT id, username, name FROM #__users', 'id'));

		// disconnect from comment save event
		$this->app->event->dispatcher->disconnect('comment:saved', array('CommentEvent', 'saved'));

		$item_objects = array();
		foreach ($items as $alias => $item) {

			if (isset($item['group'], $types[$item['group']]) && !empty($types[$item['group']]) && $type = $app_types[$types[$item['group']]]) {

				$item_obj = $this->app->object->create('Item');
				$item_obj->alias = $this->app->string->sluggify($alias);
				$item_obj->type = $type->id;

				// set a valid category alias
				$item_obj->alias = $this->app->alias->item->getUniqueAlias(0, $item_obj->alias);

				$db->query('INSERT INTO '.$table->name.'(alias) VALUES ('.$db->quote($item_obj->alias).')');
				$item_obj->id = $db->insertid();

				// set item values
				foreach ($item as $property => $value) {
					if (in_array($property, $item_vars)) {
						$item_obj->$property = $value;
					}
				}

				// fix access if j16
				if ($this->app->joomla->version->isCompatible('1.6') && $item_obj->access == 0) {
					$item_obj->access = $this->app->joomla->getDefaultAccess();
				}

				// store application id
				$item_obj->application_id = $application->id;

				// store tags
				if (isset($item['tags'])) {
					$item_obj->setTags($item['tags']);
				}

				// store author
				$item_obj->created_by_alias = "";
				if (isset($item['author'])) {
					if ($key = $authors->searchRecursive($item['author'])) {
						$item_obj->created_by = (int) $authors[$key]->id;
					} else {
						$item_obj->created_by_alias = $item['author'];
					}
				}
				// if author is unknown set current user as author
				if (!$item_obj->created_by) {
					$item_obj->created_by = $user_id;
				}

				// store modified_by
				$item_obj->modified_by = $user_id;

				// store element_data
				$item_obj->elements = $this->app->data->create();
				if (isset($item['elements'])) {
					foreach ($item['elements'] as $old_element_alias => $element) {
						if (isset($element['data'])
								&& isset($element_assignment[$item['group']][$old_element_alias][$type->id])
								&& ($element_alias = $element_assignment[$item['group']][$old_element_alias][$type->id])
								&& ($element_obj = $item_obj->getElement($element_alias))) {

							$element_obj->bindData($element['data']);
						}
					}
				}

				// set metadata, content, config params
				$item_obj->getParams()->set('metadata.', @$item['metadata']);
				$item_obj->getParams()->set('content.', @$item['content']);
				$item_obj->getParams()->set('config.', @$item['config']);

				$item_objects[$alias] = $item_obj;

				// save item -> category relationship
				if (isset($item['categories'])) {

					if (isset($item['config']['primary_category'], $categories[$item['config']['primary_category']])) {
						$item_obj->getParams()->set('config.primary_category', $categories[$item['config']['primary_category']]->id);
					} else if (isset($item['config']['primary_category']) && $id = $this->app->alias->category->translateAliasToID($item['config']['primary_category'])) {
						$item_obj->getParams()->set('config.primary_category', $id);
					}

					$item_categories = array();
					foreach ($item['categories'] as $category_alias) {
						if (isset($categories[$category_alias]) || $category_alias == '_root') {
							$item_categories[] = $category_alias == '_root' ? 0 : (int) $categories[$category_alias]->id;
						} else if ($id = $this->app->alias->category->translateAliasToID($category_alias)) {
							$item_categories[] = $id;
						}
					}

					if (!empty($item_categories)) {
						$this->app->category->saveCategoryItemRelations($item_obj, $item_categories);
					}
				}

				// save comments
				if (isset($item['comments']) && is_array($item['comments'])) {
					$comments = array();
					foreach ($item['comments'] as $key => $comment) {
						$comment_obj = $this->app->object->create('comment');
						$comment_obj->item_id = $item_obj->id;

						// set item values
						foreach ($comment as $property => $value) {
							if (in_array($property, $comment_vars)) {
								$comment_obj->$property = $value;
							}
						}

						if (isset($comment_obj->user_type) && $comment_obj->user_type == 'joomla') {
							if (isset($comment['username']) && ($key = $authors->searchRecursive($comment['username']))) {
								$comment_obj->user_id = (int) $authors[$key]->id;
								$comment_obj->author = (string) $authors[$key]->name;
							} else {
								$comment_obj->user_id = $comment_obj->user_type = '';
							}
						}
						$comment_table->save($comment_obj);
						$comments[$key] = $comment_obj;
					}
					// sanatize parent ids
					foreach ($comments as $key => $comment) {
						if ($comment->parent_id && isset($comments[$comment->parent_id])) {
							$comment->parent_id = $comments[$comment->parent_id]->id;
							$comment_table->save($comment);
						}
					}
				}
			}
		}

		foreach ($item_objects as $item) {

			foreach ($item->getElements() as $element) {

				// sanatize relateditems elements
				if ($element->getElementType() == 'relateditems') {
					$relateditems = $element->get('item', array());
					$new_related_items = array();
					foreach ($relateditems as $relateditem) {
						if (isset($items[$relateditem])) {
							$new_related_items[] = $item_objects[$relateditem]->id;
						}
					}
					$element->set('item', $new_related_items);

					// sanitize relatedcategories elements aliases
				} else if ($element->getElementType() == 'relatedcategories') {
					$relatedcategories = $element->get('category', array());
					$new_related_categories = array();
					foreach ($relatedcategories as $relatedcategory) {
						if (isset($categories[$relatedcategory])) {
							$new_related_categories[] = $categories[$relatedcategory]->id;
						} else if ($id = $this->app->alias->category->translateAliasToID($relatedcategory)) {
							$new_related_categories[] = $id;
						}
					}
					$element->set('category', $new_related_categories);
				}
			}

			try {

				$table->save($item);

			} catch (AppException $e) {
				$this->app->error->raiseNotice(0, JText::_('Error Importing Item').' ('.$e.')');
			}
		}

		return $item_objects;
	}

	/**
	 * Builds the assign element info from JSON.
	 *
	 * @param AppData $data the export data
	 *
	 * @return array Assign element info
	 */
	public function getImportInfo(AppData $data) {

		$info = array();

		$application = $this->app->zoo->getApplication();

		// get frontpage count
		$info['frontpage_count'] = (bool) $data->find('categories._root');

		// get category count
		$info['category_count'] = max(array(count($data->get('categories', array())) - ((int) $info['frontpage_count']), 0));

		// get types
		$type_elements = array();
		foreach ($application->getTypes() as $type) {
			foreach ($type->getElements() as $element) {
				$type_elements[$type->id][$element->getElementType()][] = $element;
			}
		}

		// get item types
		$info['items'] = array();
		foreach ($data->get('items', array()) as $alias => $item) {
			$group = $item['group'];
			if (!isset($info['items'][$group])) {
				$info['items'][$group]['item_count'] = 0;
				$info['items'][$group]['elements'] = array();
				if (isset($item['elements'])) {
					foreach ($item['elements'] as $alias => $element) {
						if (!isset($info['items'][$group]['elements'][$alias])) {

							// add element type
							$info['items'][$group]['elements'][$alias]['type'] = ucfirst($element['type']);

							// add element name
							$info['items'][$group]['elements'][$alias]['name'] = $element['name'];

							// add elements to assign too
							$info['items'][$group]['elements'][$alias]['assign'] = array();
							foreach ($type_elements as $type => $assign_elements) {
								if (isset($assign_elements[$element['type']])) {
									$info['items'][$group]['elements'][$alias]['assign'][$type] = $assign_elements[$element['type']];
								}
							}
						}
					}
				}
			}
			$info['items'][$group]['item_count'] += 1;
		}

		return $info;
	}

	/**
	 * Import from JSON file.
	 *
	 * @param string $file The csv file
	 * @param string $type The type to import to
	 * @param boolean $contains_headers does the csv file contain a header row
	 * @param string $field_separator the field separator
	 * @param string $field_enclosure the field enclosure
	 * @param string $element_assignment the element assignment
	 *
	 * @return boolean true on success
	 * @throws ImportHelperException
	 * @since 2.0
	 */
	public function importCSV($file, $type = '', $contains_headers = false, $field_separator = ',', $field_enclosure = '"', $element_assignment = array()) {

		// set_time_limit doesn't work in safe mode
		if (!ini_get('safe_mode')) {
			@set_time_limit(0);
		}

		// get application
		if (!$application = $this->app->zoo->getApplication()) {
			throw new ImportHelperException('No application to import too.');
		}

		if (!$type_obj = $application->getType($type)) {
			throw new ImportHelperException('Could not find type.');
		}

		$assignments = array();
		foreach ($element_assignment as $column => $value) {
			if (!empty($value[$type])) {
				$assignments[$value[$type]][] = $column;
			}
		}

		if (!isset($assignments['_name'])) {
			throw new ImportHelperException('No item name was assigned.');
		}

		// make sure the line endings are recognized irrespective of the OS
		ini_set('auto_detect_line_endings', true);

		if (($handle = fopen($file, "r")) === FALSE) {
			throw new ImportHelperException('Could not open csv file.');
		}

		$item_table = $this->app->table->item;
		$category_table = $this->app->table->category;
		$user_id = $this->app->user->get()->get('id');
		$now = $this->app->date->create()->toSQL();
		$access = $this->app->joomla->getDefaultAccess();
		$app_categories = $application->getCategories();
		$app_category_names = array_map(create_function('$cat', 'return $cat->name;'), $app_categories);
		$app_category_alias = array_map(create_function('$cat', 'return $cat->alias;'), $app_categories);
		$alias_matches = array();

		while (($data = fgetcsv($handle, 0, $field_separator, $field_enclosure)) !== FALSE) {
			if ($contains_headers) {
				$contains_headers = false;
				continue;
			}

			$item = false;

			// First check: is there an _id specified? if so, try to load the item
			if (isset($assignments['_id']) && is_array($assignments['_id'])) {
				$column = current($assignments['_id']);
				if ($id = (int) @$data[$column]) {
					$item = $item_table->get($id);
					if ($item->application_id != $application->id) {
						$item = false;
					}
				}
			}

			if (!$item) {
				$item = $this->app->object->create('Item');
				$item->application_id = $application->id;
				$item->type = $type;

				// set access
				$item->access = $access;

				// store created by
				$item->created_by = $user_id;

				// set created, modified
				$item->created = $item->modified = $now;

				// store modified_by
				$item->modified_by = $user_id;
			}

			// store element_data and item name
			$item_categories = array();
			$tags = array();
			$elements = $item->getElements();
			foreach ($assignments as $assignment => $columns) {
				$column = current($columns);
				switch ($assignment) {
					case '_name':
						$item->name = trim(@$data[$column]);
						break;
					case '_alias':
						$item->alias = $this->app->string->sluggify(@$data[$column]);
						break;
					case '_created_by_alias':
						$item->created_by_alias = @$data[$column];
						break;
					case '_created':
						if (!empty($data[$column])) {
							$item->created = $data[$column];
						}
						break;
					default:
						if (substr($assignment, 0, 9) == '_category') {
							foreach ($columns as $column) {
								$item_categories[] = @$data[$column];
							}
						} else if (substr($assignment, 0, 4) == '_tag') {
							foreach ($columns as $column) {
								$tags[] = @$data[$column];
							}
						} else if (isset($elements[$assignment])) {
							switch ($elements[$assignment]->getElementType()) {
								case 'text':
								case 'textarea':
								case 'link':
								case 'email':
								case 'date':
									$element_data = array();
									foreach ($columns as $column) {
										if (is_numeric($data[$column]) || !empty($data[$column])) {
											$element_data[$column] = array('value' => $data[$column]);
										}
									}
									$elements[$assignment]->bindData($element_data);
									break;
								case 'country':
									$element_data = array();
									foreach ($columns as $column) {
										if (!empty($data[$column])) {
											$element_data['country'][] = $data[$column];
										}
									}
									$elements[$assignment]->bindData($element_data);
									break;
								case 'select':
								case 'radio':
								case 'checkbox':
									$element_data = array();
									foreach ($columns as $column) {
										if (is_numeric($data[$column]) || !empty($data[$column])) {
											$element_data['option'][] = $data[$column];
										}
									}
									$elements[$assignment]->bindData($element_data);
									break;
								case 'gallery':
									$data[$column] = trim(@$data[$column], '/\\');
									$elements[$assignment]->bindData(array('value' => $data[$column]));
									break;
								case 'image':
								case 'download':
									$elements[$assignment]->bindData(array('file' => @$data[$column]));
									break;
								case 'googlemaps':
									$elements[$assignment]->bindData(array('location' => @$data[$column]));
									break;
							}
						}
						break;
				}
			}

			if (empty($item->name)) {
				continue;
			}

			$item->setTags($tags);

			// If not alias was set, use the name to generate it
			if (!strlen(trim($item->alias))) {
				$item->alias = $this->app->string->sluggify($item->name);
			}

			if (empty($item->alias)) {
				$item->alias = '42';
			}

			// set a valid category alias
			$item->alias = $this->app->alias->item->getUniqueAlias($item->id, $item->alias);

			try {

				$item_table->save($item);

				// store categories
				$related_categories = array();
				foreach ($item_categories as $category_name) {
					$names = array_filter(explode('///', $category_name));
					$previous_id = 0;
					$found = true;

					for ($i = 0; $i < count($names); $i++) {

						list($name, $alias) = array_pad(explode('|||', $names[$i]), 2, false);

						// did the alias change?
						if ($alias && isset($alias_matches[$alias])) {
							$alias =  $alias_matches[$alias];
						}

						// try to find category through alias, if category is not found, try to match name
						if (!($id = array_search($alias, $app_category_alias)) && !$alias) {
							$id = array_search($name, $app_category_names);
							foreach (array_keys($app_category_names, $name) as $key) {
								if ($previous_id && isset($app_categories[$key]) && $app_categories[$key]->parent == $previous_id) {
									$id = $key;
								}
							}
						}
						if (!$found || !$id) {

							$found = false;

							$category = $this->app->object->create('Category');
							$category->application_id = $application->id;
							$category->name = trim($name);
							$category->parent = $previous_id;

							// set a valid category alias
							$category->alias = $this->app->alias->category->getUniqueAlias(0, $this->app->string->sluggify($alias ? $alias : $name));

							try {

								$category_table->save($category);
								$app_categories[$category->id] = $category;
								$app_category_names[$category->id] = $category->name;
								$app_category_alias[$category->id] = $alias_matches[$alias] = $category->alias;
								$id = $category->id;

							} catch (CategoryTableException $e) {}
						}
						if ($id && $i == count($names) - 1) {
							$related_categories[] = $id;
						} else {
							$previous_id = $id;
						}
					}
				}

				// add category to item relations
				if (!empty($related_categories)) {

					$this->app->category->saveCategoryItemRelations($item, $related_categories);

					// make first category found primary category
					if (!$item->getPrimaryCategoryId()) {
						$item->getParams()->set('config.primary_category', $related_categories[0]);
						$item_table->save($item);
					}
				}

			} catch (ItemTableException $e) {}
		}
		fclose($handle);
		return true;

	}

	/**
	 * Builds the assign element info from csv.
	 *
	 * @param string $file
	 * @param boolean $contains_headers
	 * @param string $field_separator
	 * @param string $field_enclosure
	 *
	 * @return array Assign element info
	 * @since 2.0
	 */
	public function getImportInfoCSV($file, $contains_headers = false, $field_separator = ',', $field_enclosure = '"') {

		$info = array();

		$application = $this->app->zoo->getApplication();

		// get types
		$info['types'] = array();
		foreach ($application->getTypes() as $type) {
			$info['types'][$type->id] = array();
			foreach ($type->getElements() as $element) {
				// filter elements
				if (in_array($element->getElementType(), array('text', 'textarea', 'link', 'email', 'image', 'gallery', 'download', 'date', 'googlemaps', 'country', 'select', 'radio', 'checkbox'))) {
					$info['types'][$type->id][$element->getElementType()][] = $element;
				}
			}
		}

		// get item types
		$info['item_count'] = 0;

		$info['columns'] = array();

		// make sure the line endings are recognized irrespective of the OS
		ini_set('auto_detect_line_endings', true);

		// get column names and row count
		$row = 0;
		if (($handle = fopen($file, "r")) !== false) {

			while (($data = fgetcsv($handle, 0, $field_separator, $field_enclosure)) !== false) {
				if ($row == 0) {
					// get column names from header row
					if ($contains_headers) {
						$info['columns'] = $data;
					} else {
						$info['columns'] = array_fill(0, count($data), '');
					}
				}

				// get max column count
				$row++;
			}

			// get item count
			$info['item_count'] = $contains_headers ? $row - 1 : $row;

			fclose($handle);
		}

		return $info;
	}

}

/**
 * ImportHelperException identifies an Exception in the ImportHelper class
 * @see ImportHelper
 */
class ImportHelperException extends AppException {}