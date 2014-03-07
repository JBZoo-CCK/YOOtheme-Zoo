<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class Update2412 implements iUpdate {

    /*
		Function: getNotifications
			Get preupdate notifications.

		Returns:
			Array - messages
	*/
	public function getNotifications($app) {}

    /*
		Function: run
			Performs the update.

		Returns:
			bool - true if updated successful
	*/
	public function run($app) {

		// uninstall shortcut plugin
		jimport('joomla.installer.installer');

		// set query
		$query = 'SELECT extension_id as id FROM #__extensions WHERE element = '.$app->database->Quote('zooshortcut');

		// query extension id and client id
		if ($res = $app->database->queryObject($query)) {
			$installer = new JInstaller();
			$installer->uninstall('plugin', $res->id, 0);
		}
	}

}