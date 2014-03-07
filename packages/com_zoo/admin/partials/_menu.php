<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// render menu
$menu = $this->app->menu->get('nav')
	->addFilter(array('ZooMenuFilter', 'activeFilter'))
	->addFilter(array('ZooMenuFilter', 'nameFilter'))
	->addFilter(array('ZooMenuFilter', 'versionFilter'))
	->applyFilter();

echo '<div id="nav"><div class="bar"></div>'.$menu->render(array('AppMenuDecorator', 'index')).'</div>';

/*
	Class: ZooMenuFilter
		Filter for menu class.
*/
class ZooMenuFilter {

	public static function activeFilter(AppMenuItem $item) {

		// init vars
		$id          = '';
		$app		 = App::getInstance('zoo');
		$application = $app->zoo->getApplication();
		$controller  = $app->request->getWord('controller');
		$task 		 = $app->request->getWord('task');
		$classes     = array();

		// application context
		if (!empty($application)) {
			$id = $application->id.'-'.$controller;
		}

		// application configuration
		if ($controller == 'configuration' && $task) {
			if (in_array($task, array('importfrom', 'import', 'importcsv', 'importexport'))) {
				$id .= '-importexport';
			} else {
				$id .= '-'.$task;
			}
		}

		// new application
		if ($controller == 'new') {
			$id = 'new';
		}

		// application manager
		if ($controller == 'manager') {
			$id = 'manager';
			if (in_array($task, array('types', 'addtype', 'edittype', 'editelements', 'assignelements', 'assignsubmission'))) {
				$id .= '-types';
			} elseif ($task) {
				$id .= '-'.$task;
			}
		}

		// save current class attribute
		$class = $item->getAttribute('class');
		if (!empty($class)) {
			$classes[] = $class;
		}

		// set active class
		if ($item->getId() == $id || $item->hasChild($id, true)) {
			$classes[] = 'active';
		}

		// replace the old class attribute
		$item->setAttribute('class', implode(' ', $classes));
	}

	public static function nameFilter(AppMenuItem $item) {
		if ($item->getId() != 'new' && $item->getId() != 'manager') {
			$item->setName(htmlspecialchars($item->getName(), ENT_QUOTES, 'UTF-8'));
		}
	}

	public static function versionFilter(AppMenuItem $item) {
		$app = App::getInstance('zoo');

		if ($item->getId() == 'manager') {
			if ($version = $app->zoo->version()) {
				$item->setAttribute('data-zooversion', $version);
			}
		}
	}

}