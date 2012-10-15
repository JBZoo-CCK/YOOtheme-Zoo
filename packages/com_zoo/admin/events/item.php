<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ItemEvent
		Item events.
*/
class ItemEvent {

	public static function init($event) {

		$item = $event->getSubject();

	}

	public static function saved($event) {

		$item = $event->getSubject();
		$new = $event['new'];

		JPluginHelper::importPlugin('content');
		JDispatcher::getInstance()->trigger('onContentAfterSave', array($item->app->component->self->name.'.item', &$item, $new));

		$item->app->route->clearCache();

	}

	public static function deleted($event) {

		$item = $event->getSubject();

		JPluginHelper::importPlugin('content');
		JDispatcher::getInstance()->trigger('onContentAfterDelete', array($item->app->component->self->name.'.item', &$item));

		$item->app->route->clearCache();
	}

	public static function stateChanged($event) {

		$item = $event->getSubject();
		$old_state = $event['old_state'];

		JPluginHelper::importPlugin('content');
		JDispatcher::getInstance()->trigger('onContentChangeState', array($item->app->component->self->name.'.item', array($item->id), $item->state));

		$item->app->route->clearCache();
	}

	public static function beforeDisplay($event) {

		$item = $event->getSubject();

	}

	public static function afterDisplay($event) {

		$item = $event->getSubject();
		$html = $event['html'];

	}

}
