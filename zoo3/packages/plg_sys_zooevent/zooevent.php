<?php
/**
* @package   System - ZOO Event
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemZooevent extends JPlugin {

	public $app;

	/**
	 * onAfterInitialise handler
	 *
	 * Adds ZOO event listeners
	 *
	 * @access public
	 * @return null
	 */
	public function onAfterInitialise() {

		// make sure ZOO exists
//		if (!JComponentHelper::getComponent('com_zoo', true)->enabled) {
//			return;
//		}

		// load ZOO config
//		jimport('joomla.filesystem.file');
//		if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php') || !JComponentHelper::getComponent('com_zoo', true)->enabled) {
//			return;
//		}
//		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// make sure App class exists
//		if (!class_exists('App')) {
//			return;
//		}

		// Here are a number of events for demonstration purposes.
		// Have a look at administrator/components/com_zoo/config.php
		// and also at administrator/components/com_zoo/events/

		// Get the ZOO App instance
//		$zoo = App::getInstance('zoo');

		// register event
//		$zoo->event->dispatcher->connect('item:saved', array('plgSystemZooevent', 'itemSaved'));

		// register and connect events

//		$zoo->event->register('ApplicationEvent');
//		$zoo->event->dispatcher->connect('application:installed', array('ApplicationEvent', 'installed'));
//		$zoo->event->dispatcher->connect('application:init', array('ApplicationEvent', 'init'));
//		$zoo->event->dispatcher->connect('application:saved', array('ApplicationEvent', 'saved'));
//		$zoo->event->dispatcher->connect('application:deleted', array('ApplicationEvent', 'deleted'));
//		$zoo->event->dispatcher->connect('application:addmenuitems', array('ApplicationEvent', 'addmenuitems'));
//		$zoo->event->dispatcher->connect('application:configparams', array('ApplicationEvent', 'configparams'));
//		$zoo->event->dispatcher->connect('application:sefbuildroute', array('ApplicationEvent', 'sefbuildroute'));
//		$zoo->event->dispatcher->connect('application:sefparseroute', array('ApplicationEvent', 'sefparseroute'));
//		$zoo->event->dispatcher->connect('application:sh404sef', array('ApplicationEvent', 'sh404sef'));
//
//		$zoo->event->register('CategoryEvent');
//		$zoo->event->dispatcher->connect('category:init', array('CategoryEvent', 'init'));
//		$zoo->event->dispatcher->connect('category:saved', array('CategoryEvent', 'saved'));
//		$zoo->event->dispatcher->connect('category:deleted', array('CategoryEvent', 'deleted'));
//		$zoo->event->dispatcher->connect('category:stateChanged', array('CategoryEvent', 'stateChanged'));
//
//		$zoo->event->register('ItemEvent');
//		$zoo->event->dispatcher->connect('item:init', array('ItemEvent', 'init'));
//		$zoo->event->dispatcher->connect('item:saved', array('ItemEvent', 'saved'));
//		$zoo->event->dispatcher->connect('item:deleted', array('ItemEvent', 'deleted'));
//		$zoo->event->dispatcher->connect('item:stateChanged', array('ItemEvent', 'stateChanged'));
//		$zoo->event->dispatcher->connect('item:beforedisplay', array('ItemEvent', 'beforeDisplay'));
//		$zoo->event->dispatcher->connect('item:afterdisplay', array('ItemEvent', 'afterDisplay'));
//		$zoo->event->dispatcher->connect('item:beforeSaveCategoryRelations', array('ItemEvent', 'beforeSaveCategoryRelations'));
//		$zoo->event->dispatcher->connect('item:orderquery', array('ItemEvent', 'orderquery'));
//
//		$zoo->event->register('CommentEvent');
//		$zoo->event->dispatcher->connect('comment:init', array('CommentEvent', 'init'));
//		$zoo->event->dispatcher->connect('comment:saved', array('CommentEvent', 'saved'));
//		$zoo->event->dispatcher->connect('comment:deleted', array('CommentEvent', 'deleted'));
//		$zoo->event->dispatcher->connect('comment:stateChanged', array('CommentEvent', 'stateChanged'));
//
//		$zoo->event->register('SubmissionEvent');
//		$zoo->event->dispatcher->connect('submission:init', array('SubmissionEvent', 'init'));
//		$zoo->event->dispatcher->connect('submission:beforesave', array('SubmissionEvent', 'beforeSave'));
//		$zoo->event->dispatcher->connect('submission:saved', array('SubmissionEvent', 'saved'));
//		$zoo->event->dispatcher->connect('submission:deleted', array('SubmissionEvent', 'deleted'));
//
//		$zoo->event->register('ElementEvent');
//		$zoo->event->dispatcher->connect('element:download', array('ElementEvent', 'download'));
//		$zoo->event->dispatcher->connect('element:configform', array('ElementEvent', 'configForm'));
//		$zoo->event->dispatcher->connect('element:configparams', array('ElementEvent', 'configParams'));
//		$zoo->event->dispatcher->connect('element:configxml', array('ElementEvent', 'configXML'));
//		$zoo->event->dispatcher->connect('element:afterdisplay', array('ElementEvent', 'afterDisplay'));
//		$zoo->event->dispatcher->connect('element:beforedisplay', array('ElementEvent', 'beforeDisplay'));
//		$zoo->event->dispatcher->connect('element:aftersubmissiondisplay', array('ElementEvent', 'afterSubmissionDisplay'));
//		$zoo->event->dispatcher->connect('element:beforesubmissiondisplay', array('ElementEvent', 'beforeSubmissionDisplay'));
//		$zoo->event->dispatcher->connect('element:beforeedit', array('ElementEvent', 'beforeEdit'));
//		$zoo->event->dispatcher->connect('element:afteredit', array('ElementEvent', 'afterEdit'));
//
//		$zoo->event->register('LayoutEvent');
//		$zoo->event->dispatcher->connect('layout:init', array('LayoutEvent', 'init'));
//
//		$zoo->event->register('TagEvent');
//		$zoo->event->dispatcher->connect('tag:saved', array('TagEvent', 'saved'));
//		$zoo->event->dispatcher->connect('tag:deleted', array('TagEvent', 'deleted'));
//
//		$zoo->event->register('TypeEvent');
//		$zoo->event->dispatcher->connect('type:beforesave', array('TypeEvent', 'beforesave'));
//		$zoo->event->dispatcher->connect('type:aftersave', array('TypeEvent', 'aftersave'));
//		$zoo->event->dispatcher->connect('type:copied', array('TypeEvent', 'copied'));
//		$zoo->event->dispatcher->connect('type:deleted', array('TypeEvent', 'deleted'));
//		$zoo->event->dispatcher->connect('type:editdisplay', array('TypeEvent', 'editDisplay'));
//		$zoo->event->dispatcher->connect('type:coreconfig', array('TypeEvent', 'coreconfig'));
//		$zoo->event->dispatcher->connect('type:assignelements', array('TypeEvent', 'assignelements'));

	}

	public function itemSaved($event) {

		//$item = $event->getSubject();
		//$new = $event['new'];

		// do whatever you'd like to do

	}

}
