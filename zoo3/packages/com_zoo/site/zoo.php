<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

// get ZOO app
$zoo = App::getInstance('zoo');

// init vars
$path = dirname(__FILE__);

// register paths
$zoo->path->register($path.'/assets', 'assets');
$zoo->path->register($path.'/controllers', 'controllers');

// add default js
$zoo->document->addScript('assets:js/responsive.js');
$zoo->document->addScript('component.site:assets/js/default.js');

try {

	// load and dispatch application
	if ($application = $zoo->zoo->getApplication()) {
		$application->dispatch();
	} else {
		return $zoo->error->raiseError(404, JText::_('Application not found'));
	}

} catch (AppException $e) {
	$zoo->error->raiseError(500, $e);
}