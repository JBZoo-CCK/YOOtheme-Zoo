<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The helper class for applications
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ApplicationHelper extends AppHelper {

	/**
	 * Get all applications for an application group.
	 *
	 * @param string $group Application group
	 *
	 * @return array The applications of the application group
	 *
	 * @since 2.0
	 */
    public function getApplications($group = false) {
        // get application instances for selected group
        $applications = array();
        if ($table = $this->app->table->application->all(array('order' => 'name'))) {
	        foreach ($table as $application) {
	            if (!$group || $application->getGroup() == $group) {
	                $applications[$application->id] = $application;
	            }
	        }
        }
	    return $applications;
    }

	/**
	 * Get all application groups.
	 *
	 * @return array The application groups
	 *
	 * @since 2.0
	 */
	public function groups() {

		// get applications
		$apps = array();

		if ($folders = $this->app->path->dirs('applications:')) {
			foreach ($folders as $folder) {
				if ($this->app->path->path("applications:$folder/application.xml")) {
					$apps[$folder] = $this->app->object->create('Application');
					$apps[$folder]->setGroup($folder);
				}
			}
		}

		return $apps;
	}

    public function getAlphaIndex($application) {

		// set alphaindex
		$alpha_index = $this->app->alphaindex->create($application->getPath().'/config/alphaindex.xml');

		// add categories
		$add_alpha_index = $application->getParams('site')->get('config.alpha_index', 0);

		if ($add_alpha_index == 1 || $add_alpha_index == 3) {
            $categories = $application->getCategoryTree(true, $this->app->user->get(), true);
			$alpha_index->addObjects(array_filter($categories, create_function('$category', 'return $category->id != 0 && $category->totalItemCount();')), 'name');
		}
		// add items
		if ($add_alpha_index == 2 || $add_alpha_index == 3) {

			$db = $this->app->database;

			// get date
			$date = $this->app->date->create();
			$now  = $db->Quote($date->toSQL());
			$null = $db->Quote($db->getNullDate());

			$query = 'SELECT DISTINCT BINARY CONVERT(LOWER(LEFT(name, 1)) USING utf8) letter'
					.' FROM ' . ZOO_TABLE_ITEM
					.' WHERE id IN (SELECT item_id FROM ' . ZOO_TABLE_CATEGORY_ITEM . ')'
					.' AND application_id = '.(int) $application->id
					.' AND '.$this->app->user->getDBAccessString()
					.' AND state = 1'
					.' AND (publish_up = '.$null.' OR publish_up <= '.$now.')'
					.' AND (publish_down = '.$null.' OR publish_down >= '.$now.')';

			$alpha_index->addObjects($db->queryObjectList($query), 'letter');
		}
		return $alpha_index;
	}

}
