<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (!class_exists('App')) {

	// init vars
	$path = dirname(__FILE__);

	// load imports
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');
	jimport('joomla.filesystem.path');
	jimport('joomla.user.helper');
	jimport('joomla.mail.helper');

	// load classes
	JLoader::register('App', $path.'/classes/app.php');
	JLoader::register('AppController', $path.'/classes/controller.php');
	JLoader::register('AppHelper', $path.'/classes/helper.php');
	JLoader::register('AppView', $path.'/classes/view.php');
	JLoader::register('ComponentHelper', $path.'/helpers/component.php');
	JLoader::register('PathHelper', $path.'/helpers/path.php');
	JLoader::register('UserAppHelper', $path.'/helpers/user.php');

}