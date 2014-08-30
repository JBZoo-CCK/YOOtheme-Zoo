<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class Update320 implements iUpdate {

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
        // add asset_id to application table
        $fields = $app->database->getTableColumns(ZOO_TABLE_APPLICATION);
        if (!array_key_exists('asset_id', $fields)) {
            $app->database->query('ALTER TABLE '.ZOO_TABLE_APPLICATION.' ADD `asset_id` int(10) UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'FK to the #__assets table.\' AFTER `id`');
        }
        // fix rgt value in old ZOO demo installations
        $result = $app->database->queryResult('SELECT MAX(`rgt`) + 1 FROM `#__assets`');
        $app->database->query('UPDATE `#__assets` SET `rgt` = '.$result.' WHERE `id`=1');
    }
}