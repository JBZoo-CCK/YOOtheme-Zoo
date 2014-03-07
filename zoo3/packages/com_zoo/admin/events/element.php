<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Deals with element events.
 *
 * @package Component.Events
 */
class ElementEvent {

	/**
	 * Placeholder for the beforeDisplay event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function beforeDisplay($event) {

		$item = $event->getSubject();
		$element = $event['element'];

		// element will not be rendered if $event['render'] is set to false
		// $event['render'] = false;

	}

	/**
	 * Placeholder for the afterDisplay event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function afterDisplay($event) {

		$item = $event->getSubject();
		$element = $event['element'];

		// set $event['html'] after modifying the html
		$html = $event['html'];
		$event['html'] = $html;
	}

	/**
	 * Placeholder for the beforeSubmissionDisplay event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function beforeSubmissionDisplay($event) {

		$item = $event->getSubject();
		$element = $event['element'];

		// element will not be rendered if $event['render'] is set to false
		// $event['render'] = false;

	}

	/**
	 * Placeholder for the afterSubmissionDisplay event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function afterSubmissionDisplay($event) {

		$item = $event->getSubject();
		$element = $event['element'];

		// set $event['html'] after modifying the html
		$html = $event['html'];
		$event['html'] = $html;
	}

	/**
	 * Placeholder for the configParams event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function configParams($event) {

		$element = $event->getSubject();

		// set events ReturnValue after modifying $params
		$params = $event->getReturnValue();
		$event->setReturnValue($params);

	}

	/**
	 * Set the name and description parameters for the core elements to hidden
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function configForm($event) {

		$element = $event->getSubject();
		if ($element->getGroup() == 'Core') {
			$form = $event['form'];
			if ($xml = $form->getXML('_default')) {
				foreach ($xml->xpath('//param[@name="name"]') as $child) {
					$dom = dom_import_simplexml($child);
					$dom->setAttribute('type', 'hidden');
				}
			}
		}

	}

	/**
	 * Placeholder for the configXML event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function configXML($event) {

		$element = $event->getSubject();
		$xml = $event['xml'];

	}

	/**
	 * Placeholder for the download event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function download($event) {

		$download_element = $event->getSubject();
		$check = $event['check'];
	}

	/**
	 * Placeholder for the afterEdit event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function afterEdit($event) {

		$element = $event->getSubject();

		// set $event['html'] after modifying the html
		$html = $event['html'];
		$event['html'] = $html;
	}

}