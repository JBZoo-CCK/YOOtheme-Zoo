<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The dependency helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class DependencyHelper extends AppHelper {

	/**
	 * Checks if ZOO extensions meet the required version
	 *
	 * @return boolean true if all requirements are met
	 *
	 * @since 2.0
	 */
	public function check() {
		if ($dependencies = $this->app->path->path("component.admin:installation/dependencies.config")) {
			if ($dependencies = json_decode(file_get_contents($dependencies))) {
				foreach ($dependencies as $dependency) {
					$required  = $dependency->version;
					$manifest = $this->app->path->path('root:'.$dependency->manifest);
					if ($required && is_file($manifest) && is_readable($manifest)) {
						if ($xml = simplexml_load_file($manifest)) {
							if (version_compare($required, (string) $xml->version) > 0) {
								$name = isset($dependency->url) ? "<a href=\"{$dependency->url}\">{$xml->name}</a>" : (string) $xml->name;
								$this->app->error->raiseNotice(0, sprintf("The %s extension requires an update for the Zoo to run correctly.", $name));
							}
						}
					}
				}
			}
		}
	}

}