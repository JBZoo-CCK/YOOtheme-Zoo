<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper class for submissions
 *
 * @package Component.Helpers
 * @since 2.0
 */
class SubmissionHelper extends AppHelper {

	/**
	 * Remove html from data
	 *
	 * @param Traversable|object|string $data
	 *
	 * @return Traversable|object|string the filtered data
	 * @since 2.0
	 */
	public function filterData($data) {

		if (is_array($data) || $data instanceof Traversable) {

			$result = array();
			foreach ($data as $key => $value) {
				$result[$key] = $this->filterData($value);
			}
			return $result;
		} elseif (is_object($data)) {

			$result = new stdClass();
			foreach (get_object_vars($data) as $key => $value) {
				$result->$key = $this->filterData($value);
			}
			return $result;
		} else {

			// remove all html tags or escape if in [code] tag
			$data = preg_replace_callback('/\[code\](.+?)\[\/code\]/is', create_function('$matches', 'return htmlspecialchars($matches[0]);'), $data);
			$data = strip_tags($data);

			return $data;
		}
	}

	/**
	 * Retrieve hash of submission, type, item.
	 *
	 * @param int $submission_id
	 * @param string $type_id
	 * @param int $item_id
	 * @param bool $edit
	 *
	 * @return string The resulting hash
	 * @since 2.0
	 */
	public function getSubmissionHash($submission_id, $type_id, $item_id = 0, $edit = false) {

		$item_id = empty($item_id) ? 0 : $item_id;

		return $this->app->system->getHash($submission_id.$type_id.$item_id.$edit);
	}

	/**
	 * Retrieve params for frontend editing submisson
	 *
	 * @param array $types
	 *
	 * @return ParameterData submission params
	 * @since 3.2
	 */
	public function getSubmissionEditParams(array $types) {
		$params = $this->app->parameter->create();
		foreach ($types as $type) {
			$params->set('form.'.$type->id, array('layout' => 'edit', 'category' => ''));
		}
		$params->set('trusted_mode', 1);

		return $params;
	}

	/**
	 * Send notification email
	 *
	 * @param Item $item Item
	 * @param array $recipients Array email => name
	 * @param string $layout The layout
	 *
	 * @since 2.0
	 */
	public function sendNotificationMail($item, $recipients, $layout) {

		// workaround to make sure JSite is loaded
		$this->app->loader->register('JSite', 'root:includes/application.php');

		// init vars
		$website_name = $this->app->system->config->get('sitename');
		$item_link = JURI::root().'administrator/index.php?'.http_build_query(array(
				'option' => $this->app->component->self->name,
				'controller' => 'item',
                'changeapp' => $item->application_id,
				'task' => 'edit',
				'cid[]' => $item->id,
			), '', '&');

		// send email to $recipients
		foreach ($recipients as $email => $name) {

			if (empty($email)) {
				continue;
			}

			$mail = $this->app->mail->create();
			$mail->setSubject(JText::_("New Submission notification")." - ".$item->name);
			$mail->setBodyFromTemplate($item->getApplication()->getTemplate()->resource.$layout, compact(
				'item', 'submission', 'website_name', 'email', 'name', 'item_link'
			));
			$mail->addRecipient($email);
			$mail->Send();
		}
	}

}

/**
 * SubmissionHelperException identifies an Exception in the SubmissionHelper class
 * @see SubmissionHelper
 */
class SubmissionHelperException extends AppException {}