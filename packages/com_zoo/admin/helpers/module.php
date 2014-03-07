<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Module helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ModuleHelper extends AppHelper {

	/**
	 * Load Joomla modules.
	 *
	 * @staticvar array $modules
	 * @return boolean true on success
	 * @since 2.0
	 */
	public function load($published = false) {
		static $modules;

		if (isset($modules)) {
			return $modules;
		}

		$db = $this->app->database;

		$query = "SELECT id, title, module, position, content, showtitle, params, published"
				." FROM #__modules AS m"
				." LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id"
				." WHERE "
				."m.".$this->app->user->getDBAccessString();

		// Fetch only published modules
		if ($published) {

            // get date
            $date = $this->app->date->create();
            $now  = $db->Quote($date->toSQL());
            $null = $db->Quote($db->getNullDate());

			$query .= " AND published = 1"
					. " AND (publish_up = ".$null." OR publish_up <= ".$now.")"
					. " AND (publish_down = ".$null." OR publish_down >= ".$now.")";
		}

		$query .= " AND m.client_id = 0"
				." ORDER BY position, ordering";

		$db->setQuery($query);

		$modules = $db->loadObjectList('id');
		foreach ($modules as $module) {
			$file = $module->module;
			$custom = $this->app->string->substr($file, 0, 4) == 'mod_' ? 0 : 1;
			$module->user = $custom;
			$module->name = $custom ? $module->title : $this->app->string->substr($file, 4);
			$module->style = null;
			$module->position = $this->app->string->strtolower($module->position);
		}

		return $modules;
	}

	/**
	 * Enable Joomla module.
	 *
	 * @param string $module
	 * @param string $position
	 * @param int $menuid
	 *
	 * @since 2.0
	 */
	public function enable($module, $position, $menuid = 0) {

		$query = "UPDATE #__modules, (SELECT MAX(ordering) +1 as ord FROM #__modules WHERE position = '$position') tt"
				." SET published = 1, position = '$position', ordering = tt.ord"
				." WHERE module = '$module'";
		$this->app->database->query($query);

		$query = "INSERT IGNORE #__modules_menu"
				." SET menuid = $menuid, moduleid = (SELECT id FROM #__modules WHERE module = '$module')";
		$this->app->database->query($query);

		$query = "UPDATE #__extensions, (SELECT MAX(ordering) +1 as ord FROM #__modules WHERE position = '$position') tt"
				." SET enabled = 1, ordering = tt.ord"
				." WHERE element = '$module'";
		$this->app->database->query($query);

	}

	/**
	 * Get items from ZOO module params.
	 *
	 * @param AppData $params Module Parameter
	 * @return array Items
	 */
	public function getItems($params) {

		$items = array();
		if ($application = $this->app->table->application->get($params->get('application', 0))) {

			// set one or multiple categories
			$category = (int) $params->get('category', 0);
			if ($params->get('subcategories')) {
				$categories = $application->getCategoryTree(true);
				if (isset($categories[$category])) {
					$category = array_merge(array($category), array_keys($categories[$category]->getChildren(true)));
				}
			}

			// get items
			if ($params->get('mode') == 'item') {
				if (($item = $this->app->table->item->get($params->get('item_id'))) && $item->isPublished() && $item->canAccess()) {
					$items[] = $item;
				}
			} else if ($params->get('mode') == 'types') {
				$items = $this->app->table->item->getByType($params->get('type'), $application->id, true, null, $params->get('order', array('_itemname')), 0, $params->get('count', 4));
			} else {
				$items = $this->app->table->item->getByCategory($application->id, $category, true, null, $params->get('order', array('_itemname')), 0, $params->get('count', 4));
			}
		}
		return $items;
	}

}