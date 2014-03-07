<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Twitter helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class TwitterHelper extends AppHelper {

	/**
	 * Get Twitter Client.
	 *
	 * @param Application The application to get params from
	 *
	 * @return Twitter|null The Twitter client
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

		// load twitter classes
		$this->app->loader->register('TwitterOAuth', 'libraries:twitter/twitteroauth.php');

		$oauth_token = null;
		$oauth_token_secret = null;
		if (isset($_SESSION['twitter_oauth_token'], $_SESSION['twitter_oauth_token_secret'])) {
			$oauth_token = $_SESSION['twitter_oauth_token'];
			$oauth_token_secret = $_SESSION['twitter_oauth_token_secret'];
		}

		// Build TwitterOAuth object with client credentials.
		return new TwitterOAuth($params->get('twitter_consumer_key'), $params->get('twitter_consumer_secret'), $oauth_token, $oauth_token_secret);

	}

	/**
	 * Get Twitter Fields.
	 *
	 * @param string $t_uid Twitter user id
	 * @param array $fields Fields to acquire
	 * @param Application The application to get params from
	 *
	 * @return array The fields
	 * @since 2.0
	 */
	public function fields($t_uid, $fields = null, $application = null) {

		try {

			$connection = $this->client($application);
			if ($connection) {
				$infos = $connection->get('users/show.json?user_id='.$t_uid.'&include_entities=true');

				if (is_object($infos)) {
					if (is_array($fields)) {
						return array_intersect_key((array) $infos, array_flip($fields));
					} else {
						return (array) $infos;
					}
				}
			}

		} catch (Exception $e) {}
	}

	/**
	 * Logout from Twitter.
	 *
	 * @return self
	 * @since 2.0
	 */
	public function logout() {
		// remove access token from session
		$_SESSION['twitter_oauth_token'] = null;
		$_SESSION['twitter_oauth_token_secret'] = null;
		return $this;
	}

}
