<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Deals with application events.
 *
 * @package Component.Events
 */
class ApplicationEvent {

	/**
	 * When an application is loaded on the frontend,
	 * load the language files from the app folder too
	 *
	 * @param  AppEvent 	$event The event triggered
	 */
	public static function init($event) {

		$application = $event->getSubject();
        $app         = $application->app;

        $app->path->register($app->path->path($application->getResource().'elements'), 'elements');

		// load site language
		if ($app->system->application->isSite()) {
			$app->system->language->load('com_zoo', $application->getPath(), null, true);

            if ($template = $application->getTemplate()) {
                $app->path->register($template->getPath().'/elements', 'elements');
            }
		}

	}

	/**
	 * Placeholder for the saved event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function saved($event) {

		$application = $event->getSubject();
		$new = $event['new'];

	}

	/**
	 * Placeholder for the deleted event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function deleted($event) {

		$application = $event->getSubject();

	}

	/**
	 * Placeholder for the installed event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function installed($event) {

		$application = $event->getSubject();
		$update = $event['update'];

	}

	/**
	 * Placeholder for the addmenuitems event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function addmenuitems($event) {

		$application = $event->getSubject();

		// Tab object
		$tab = $event['tab'];

		// add child

		// return the tab object
		$event['tab'] = $tab;
	}

	/**
	 * Placeholder for the configParams event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function configParams($event) {

		$application = $event->getSubject();

		// set events ReturnValue after modifying $params
		$params = $event->getReturnValue();

        $params[] = '<application><params group="application-config"><param name="test" type="text" size="3" default="15" label="Test Param" description="Test Param Description" /></params></application>';

		$event->setReturnValue($params);

	}

}
