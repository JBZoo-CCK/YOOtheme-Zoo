<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: SubmissionEvent
		Submission events.
*/
class SubmissionEvent {

	public static function init($event) {

		$submission = $event->getSubject();

	}

	public static function beforeSave($event) {

		$submission = $event->getSubject();

	}

	public static function saved($event) {

		$submission = $event->getSubject();

		if ($event['new'] || !$submission->isInTrustedMode()) {

			// send email to admins
			if ($recipients = $submission->getParams()->get('email_notification', '')) {
				$submission->app->submission->sendNotificationMail($event['item'], array_flip(explode(',', $recipients)), 'mail.submission.new.php');
			}

		}

	}

	public static function deleted($event) {

		$submission = $event->getSubject();

	}

}
