<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Facebook helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class FacebookHelper extends AppHelper {

	/**
	 * Get Facebook Client.
	 *
	 * @param Application The application to get params from
	 *
	 * @return Facebook|null The Facebook client
	 * @since 2.0
	 */
	public function client($application = null) {

		// get application
		if (!$application) {
			$application = $this->app->zoo->getApplication();
		}

		// get comment params
		$params = $this->app->parameter->create()->loadArray($application ? $application->getParams()->get('global.comments.') : array());

		if (!function_exists('curl_init')) {
			return null;
		}

		// load facebook classes
		$this->app->loader->register('Facebook', 'libraries:facebook/facebook.php');

		$access_token = null;
		if (isset($_SESSION['facebook_access_token'])) {
			$access_token = $_SESSION['facebook_access_token'];
		}

		// Build FacebookOAuth object with client credentials.
		return new Facebook($this->app, array('app_id' => $params->get('facebook_app_id'), 'app_secret' => $params->get('facebook_app_secret'), 'access_token' => $access_token));
	}

	/**
	 * Get Facebook Fields.
	 *
	 * @param string $fb_uid Facebook user id
	 * @param array $fields Fields to acquire
	 * @param Application The application to get params from
	 *
	 * @return array The fields
	 * @since 2.0
	 */
	public function fields($fb_uid, $fields = null, $application = null) {
		try {

			$connection = $this->client($application);
			if ($connection) {

				$infos = $connection->getProfile($fb_uid);

				if (is_object($infos)) {
					if (is_array($fields)) {
						return array_intersect_key((array) $infos, array_flip($fields));
					} else {
						return (array) $infos;
					}
				}
			}
		} catch (Exception $e) {

		}
	}

	/**
	 * Logout from Facebook.
	 *
	 * @return self
	 * @since 2.0
	 */
	public function logout() {
		// remove access token from session
		$_SESSION['facebook_access_token'] = null;
		return $this;
	}

}
