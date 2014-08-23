<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The category helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class CategoryHelper extends AppHelper {

	protected $_cache;

	/**
	 * Class constructor
	 *
	 * @param string $app App instance.
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// get item id -> category ids cache
		// refreshes after one hour automatically
		$this->_cache = $app->cache->create($app->path->path('cache:') . '/item_category', true, 3600, 'apc');
		if ($this->_cache && !$this->_cache->check()) {
			$this->_cache = null;
		}
	}

	/**
	 * Method to retrieve item's related category ids.
	 *
	 * @param int $item_id The items id
	 * @param boolean $published Include published categories only
	 *
	 * @return array category ids
	 *
	 * @since 2.0
	 */
	public function getItemsRelatedCategoryIds($item_id, $published = false) {

		$key = $item_id.'_'.$published;
		if ($this->_cache && $result = $this->_cache->get($key)) {
			return $result;
		}

		// select item to category relations
		$query = 'SELECT b.id'
				.' FROM '.ZOO_TABLE_CATEGORY_ITEM.' AS a'
				.' JOIN '.ZOO_TABLE_CATEGORY.' AS b ON a.category_id = b.id'
				.' WHERE a.item_id='.(int) $item_id
				.($published == true ? ' AND b.published = 1' : '')
				.' UNION SELECT 0'
				.' FROM '.ZOO_TABLE_CATEGORY_ITEM.' AS a'
				.' WHERE a.item_id='.(int) $item_id.' AND a.category_id = 0';

		$result = $this->app->database->queryResultArray($query);
		if ($this->_cache) {
			$this->_cache->set($key, $result);
			$this->_cache->save();
		}
		return $result;
	}

	/**
	 * Method to add category related items.
	 *
	 * @param Item  $item 		The item
	 * @param array $categories The category ids
	 *
	 * @return boolean true on success
	 *
	 * @since 2.0
	 */
	public function saveCategoryItemRelations($item, $categories) {
		// check ACL
		if (!$item->canEdit()) {
			return false;
		}

		//init vars
		$db = $this->app->database;

		if (!is_array($categories)) {
			$categories = array($categories);
		}

		// trigger an event to let 3rd party extend the category list
		$this->app->event->dispatcher->notify($this->app->event->create($item, 'item:beforeSaveCategoryRelations', array('categories' => &$categories)));

		$categories = array_unique($categories);

		// delete category to item relations
		$query = "DELETE FROM ".ZOO_TABLE_CATEGORY_ITEM
				." WHERE item_id=".(int) $item->id;

		// execute database query
		$db->query($query);

		// Generate the sql query for the categories
		$query_string = '(%s,'.(int) $item->id.')';
		$category_strings = array();
		foreach ($categories as $category) {
			if (is_numeric($category)) {
				$category_strings[] = sprintf($query_string, $category);
			}
		}

		// add category to item relations
		// insert relation to database
		if (!empty($category_strings)) {
			$query = "INSERT INTO ".ZOO_TABLE_CATEGORY_ITEM
					." (category_id, item_id) VALUES ".implode(',', $category_strings);

			// execute database query
			$db->query($query);
		}

		$this->clearItemCategoryCache();

		return true;
	}

	/**
	 * Method to delete category related items.
	 *
	 * @param int $category_id The category id
	 *
	 * @return int number of affected rows
	 *
	 * @since 2.0
	 */
	public function deleteCategoryItemRelations($category_id) {
		// check ACL
		if (!$this->app->zoo->getApplication()->canManageCategories()) {
			return false;
		}

		// delete category to item relations
		$query = "DELETE FROM ".ZOO_TABLE_CATEGORY_ITEM
				." WHERE category_id = ".(int) $category_id;

		// execute database query
		$result = $this->app->database->query($query);
		$this->clearItemCategoryCache();
		return $result;
	}

	/**
	 * Method to clear the item category cache
	 *
	 * @since 2.6.5
	 */
	public function clearItemCategoryCache() {
		if ($this->_cache) {
			$this->_cache->clear()->save();
		}
	}

}