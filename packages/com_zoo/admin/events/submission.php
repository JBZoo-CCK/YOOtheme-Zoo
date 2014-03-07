<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Deals with submission events.
 *
 * @package Component.Events
 */
class SubmissionEvent {

	/**
	 * Placeholder for the init event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function init($event) {

		$submission = $event->getSubject();

	}

	/**
	 * Placeholder for the beforeSave event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function beforeSave($event) {

		$submission = $event->getSubject();

	}

	/**
	 * Sends notification emails to the submission notification recipients
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function saved($event) {

		$submission = $event->getSubject();

		if ($event['new'] || !$submission->isInTrustedMode()) {

			// send email to admins
			if ($recipients = $submission->getParams()->get('email_notification', '')) {
				$submission->app->submission->sendNotificationMail($event['item'], array_flip(explode(',', $recipients)), 'mail.submission.new.php');
			}

		}

	}

	/**
	 * Placeholder for the deleted event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function deleted($event) {

		$submission = $event->getSubject();

	}

}
