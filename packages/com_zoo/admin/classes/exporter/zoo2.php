<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/
/**
 * Exporter for ZOO version 2
 *
 * @package Component.Classes.Exporters
 */
class AppExporterZoo2 extends AppExporter {

	/**
	 * The application for which we are running the exporter
	 *
	 * @var Application
	 * @since 2.0
	 */
	protected $_application;

	/**
	 * The list of categories to export
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_categories;

	/**
	 * Reference to the comment table object
	 *
	 * @var CommentTable
	 * @since 2.0
	 */
	protected $_comment_table;

	/**
	 * Class Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->_name = 'Zoo v2';
	}

	/**
	 * Do the real exporting
	 *
	 * @return string The JSON dump of the items and categories
	 *
	 * @throws AppExporterException
	 *
	 * @since 2.0
	 */
	public function export() {

		if (!$this->_application = $this->app->zoo->getApplication()) {
			throw new AppExporterException('No application selected.');
		}

		// export frontpage
		$frontpage = $this->app->object->create('Category');
		$frontpage->name  = 'Root';
		$frontpage->alias = '_root';
		$frontpage->description = $this->_application->description;

		// export categories
		$this->_categories = $this->_application->getCategories();
		$this->_categories[0] = $frontpage;
		foreach ($this->_categories as $category) {
			$this->_addZooCategory($category);
		}

		// export items
		$this->_comment_table = $this->app->table->comment;
		$item_table = $this->app->table->item;
		foreach ($this->_application->getTypes() as $type) {
			foreach ($item_table->getByType($type->id, $this->_application->id) as $key => $item) {
				$this->_addZooItem($item, $type);
				$item_table->unsetObject($key);
			}
		}

		return parent::export();

	}

	/**
	 * Add a category to the exporing list
	 *
	 * @param Category $category The category to export
	 *
	 * @return AppExporterZoo2 $this for chaining support
	 *
	 * @since 2.0
	 */
	protected function _addZooCategory(Category $category) {

		// store category attributes
		$data = array();
		foreach ($this->category_attributes as $attribute) {
			if (isset($category->$attribute)) {
				$data[$attribute] = $category->$attribute;
			}
		}

		// store category parent
		if (isset($this->_categories[$category->parent])) {
			$data['parent'] = $this->_categories[$category->parent]->alias;
		}

		// store category content params
		$data['content'] = $category->alias == '_root' ? $this->_application->getParams()->get('content.') : $category->getParams()->get('content.');

		// store category metadata params
		$data['metadata'] = $category->alias == '_root' ? $this->_application->getParams()->get('metadata.') : $category->getParams()->get('metadata.');

		parent::_addCategory($category->name, $category->alias, $data);
	}

	/**
	 * Add an item to the exporting list
	 *
	 * @param Item $item The item to export
	 * @param Type $type The type of the item
	 *
	 * @return AppExporterZoo2 $this for chaining support
	 *
	 * @since 2.0
	 */
	protected function _addZooItem(Item $item, Type $type) {

		$data = array();
		foreach ($this->item_attributes as $attribute) {
			if (isset($item->$attribute)) {
				$data[$attribute] = $item->$attribute;
			}
		}
		if ($user = $this->app->user->get($item->created_by)) {
			$data['author'] = $user->username;
		}

		$data['tags']	  = $item->getTags();

		// store item content, metadata, config params
		$data['content']  = $item->getParams()->get('content.');
		$data['metadata'] = $item->getParams()->get('metadata.');
		$data['config']   = $item->getParams()->get('config.');

		// add categories
		foreach ($item->getRelatedCategoryIds() as $category_id) {
			$alias = '';
			if (empty($category_id)) {
				$alias = '_root';
			} else if (isset($this->_categories[$category_id])) {
				$alias = $this->_categories[$category_id]->alias;
			}
			if (!empty($alias)) {
				$data['categories'][] = $alias;
			}
			if ($item->getPrimaryCategoryId() == $category_id) {
				$data['config']['primary_category'] = $alias;
			}
		}

		foreach ($item->elements as $identifier => $element_data) {

			if (!$element = $type->getElement($identifier)) {
				continue;
			}
			$element_type = $element->getElementType();

			switch ($element_type) {
				case 'relateditems':
					$items = array();
					if (isset($element_data['item'])) {
						foreach ($element_data['item'] as $item_id) {
							$items[] = $this->app->alias->item->translateIDToAlias($item_id);
						}
					}
					$element_data['item'] = $items;

					break;

				case 'relatedcategories':
					$categories = array();
					if (isset($element_data['category'])) {
						foreach ($element_data['category'] as $category_id) {
							$categories[] = isset($this->_categories[$category_id]) ? $this->_categories[$category_id]->alias : $this->app->alias->category->translateIDToAlias($category_id);
						}
					}
					$element_data['category'] = $categories;

					break;

			}

			$data['elements'][$identifier]['type'] = $element_type;
			$data['elements'][$identifier]['name'] = $element->config->get('name');
			$data['elements'][$identifier]['data'] = $element_data;

			foreach ($this->_comment_table->getCommentsForItem($item->id) as $comment) {
				foreach ($this->comment_attributes as $attribute) {
					if (isset($comment->$attribute)) {
						$data['comments'][$comment->id][$attribute] = $comment->$attribute;
					}
				}
				if ($comment->user_type == 'joomla' && ($user = $this->app->user->get($comment->user_id))) {
					$data['comments'][$comment->id]['username'] = $user->username;
				}
			}

		}

		parent::_addItem($item->name, $item->alias, $type->name, $data);
	}

}