<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: CategoryEvent
		Category events.
*/
class CategoryEvent {

	public static function init($event) {

		$category = $event->getSubject();

	}

	public static function saved($event) {

		$category = $event->getSubject();
		$new = $event['new'];

		JPluginHelper::importPlugin('content');
		JDispatcher::getInstance()->trigger('onContentAfterSave', array($category->app->component->self->name.'.category', &$category, $new));

		$category->app->route->clearCache();
	}

	public static function deleted($event) {

		$category = $event->getSubject();

		JPluginHelper::importPlugin('content');
		JDispatcher::getInstance()->trigger('onContentAfterDelete', array($category->app->component->self->name.'.category', &$category));

		$category->app->route->clearCache();

	}

	public static function stateChanged($event) {

		$category = $event->getSubject();
		$old_state = $event['old_state'];

		JPluginHelper::importPlugin('content');
		JDispatcher::getInstance()->trigger('onContentChangeState', array($category->app->component->self->name.'.category', array($category->id), $category->published));

		$category->app->route->clearCache();

	}

}
