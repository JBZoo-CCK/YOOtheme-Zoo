<?php
/**
* @package   ZOO Category
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

// get app
$zoo = App::getInstance('zoo');

// load zoo frontend language file
$zoo->system->language->load('com_zoo');

// init vars
$path = dirname(__FILE__);

//register base path
$zoo->path->register($path, 'mod_zoocategory');

// register helpers
$zoo->path->register($path, 'helpers');
$zoo->loader->register('CategoryModuleHelper', 'helpers:helper.php');

if (!$application = $zoo->table->application->get($params->get('application', 0))) {
	return null;
}

// set one or multiple categories
$categories = $application->getCategoryTree(true, null, (bool) $params->get('add_count', false));
if (($category = $categories[$params->get('category', 0)]) && $category->hasChildren()) {
	include(JModuleHelper::getLayoutPath('mod_zoocategory', $params->get('theme', 'list')));
}