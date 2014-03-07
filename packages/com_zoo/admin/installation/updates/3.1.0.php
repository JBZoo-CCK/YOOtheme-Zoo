<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class Update310 implements iUpdate {

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

        foreach (array('modules/mod_zooitem/renderer/item', 'plugins/system/widgetkit_zoo/widgets/*/renderer/item') as $dir) {
            foreach(glob(JPATH_ROOT.'/'.$dir) as $folder) {
                $file = "$folder/positions.xml";
                if (JFile::exists($file) and $content = JFile::read($file) and false === strpos($content, 'positions layout="uikit"')) {
                    if (false !== $pos = strpos($content, '</renderer>')) {
$addition = <<<EOD
    <positions layout="uikit">
        <position name="title">Title</position>
        <position name="media">Media</position>
        <position name="meta">Meta</position>
        <position name="description">Description</position>
        <position name="links">Links</position>
    </positions>

EOD;
                        $content = substr($content, 0, $pos) . $addition . substr($content, $pos);
                        JFile::write($file, $content);
                    }
                }

                $file = "$folder/metadata.xml";
                if (JFile::exists($file) and $content = JFile::read($file) and false === strpos($content, 'layout name="uikit"')) {
                    if (false !== $pos = strpos($content, '</metadata>')) {
$addition = <<<EOD
    <layout name="uikit">
        <name>UIkit</name>
        <description>This is an UIkit layout to render an item in the module themes.</description>
    </layout>

EOD;
                        $content = substr($content, 0, $pos) . $addition . substr($content, $pos);
                        JFile::write($file, $content);
                    }
                }
            }
        }
	}

}