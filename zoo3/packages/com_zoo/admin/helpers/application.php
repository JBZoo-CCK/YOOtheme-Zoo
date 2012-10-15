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
        foreach ($this->app->table->application->all(array('order' => 'name')) as $application) {
            if (!$group || $application->getGroup() == $group) {
                $applications[$application->id] = $application;
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

}
