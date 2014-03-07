<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Plugin helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class PluginHelper extends AppHelper {

	/**
	 * Enable Joomla plugin.
	 *
	 * @param string $plugin
	 *
	 * @since 2.0
	 */
	public function enable($plugin) {
		$this->app->database->query("UPDATE #__extensions SET enabled = 1 WHERE element = '$plugin'");
	}

}