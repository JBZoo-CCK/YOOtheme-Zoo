<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Deals with item events.
 *
 * @package Component.Events
 */
class ItemEvent {

	/**
	 * Placeholder for the init event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function init($event) {

		$item = $event->getSubject();

	}

	/**
	 * Triggers joomla content plugins on the item and clears the route cache
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function saved($event) {

		$item = $event->getSubject();
		$new = $event['new'];

		// Trigger the onFinderAfterSave event.
        JPluginHelper::importPlugin('finder');
		JDispatcher::getInstance()->trigger('onFinderAfterSave', array($item->app->component->self->name.'.item', &$item, $new));

        // clear item route cache on save
		$item->app->route->clearCache();

        // geocode googlemaps elements (tries to workaround googles ('You are over your quota' error)
        foreach ($item->getElementsByType('googlemaps') as $element) {
            $element->geocode();
        }
	}

	/**
	 * Triggers joomla content plugins on the item and clears the route cache
	 *
	 * @param  AppEvent $event The event triggered
	 */

	public static function deleted($event) {

		$item = $event->getSubject();

		// Trigger the onFinderAfterSave event.
        JPluginHelper::importPlugin('finder');
		JDispatcher::getInstance()->trigger('onFinderAfterDelete', array($item->app->component->self->name.'.item', &$item));

		$item->app->route->clearCache();
	}

	/**
	 * Triggers joomla content plugins on the item and clears the route cache
	 *
	 * @param  AppEvent $event The event triggered
	 */

	public static function stateChanged($event) {

		$item = $event->getSubject();
		$old_state = $event['old_state'];

		JPluginHelper::importPlugin('content');
		JDispatcher::getInstance()->trigger('onContentChangeState', array($item->app->component->self->name.'.item', array($item->id), $item->state));

		$item->app->route->clearCache();
	}

	/**
	 * Placeholder for the beforeDisplay event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function beforeDisplay($event) {

		$item = $event->getSubject();

	}

	/**
	 * Placeholder for the afterDisplay event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function afterDisplay($event) {

		$item = $event->getSubject();
		$html = $event['html'];

	}

	/**
	 * Placeholder for the beforeSaveCategoryRelations event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function beforeSaveCategoryRelations($event) {

		// The item
		$item 		= $event->getSubject();
		// The list of category ids
		$categories = $event['categories'];

	}

}
