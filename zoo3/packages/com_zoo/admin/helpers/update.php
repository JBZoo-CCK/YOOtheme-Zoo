<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Update helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class UpdateHelper extends AppHelper {

	/**
	 * The update cache
	 *
	 * @var AppCache|false
	 */
	protected $_cache;

	/**
	 * Checks if ZOO needs to be updated.
	 *
	 * @return boolean true if ZOO needs to be updated
	 * @since 2.0
	 */
	public function required() {
		$updates = $this->getRequiredUpdates();
		return !empty($updates);
	}

	/**
	 * Return required update versions.
	 *
	 * @return array versions of required updates
	 * @since 2.0
	 */
	public function getRequiredUpdates() {

		// get current version
		$current_version = $this->getVersion();

		// find required updates
		if ($files = $this->app->path->files('updates:', false, '/^\d+.*\.php$/')) {
			$files = array_map(create_function('$file', 'return basename($file, ".php");'), array_filter($files, create_function('$file', 'return version_compare("'.$current_version.'", basename($file, ".php")) < 0;')));
			usort($files, create_function('$a, $b', 'return version_compare($a, $b);'));
		}

		return $files;
	}

	/**
	 * Get preupdate notifications.
	 *
	 * @return array messages
	 * @since 2.0
	 */
	public function getNotifications() {

		// check if update is required
		if (!$this->required()) {
			return $this->_createResponse('No update required.', false, false);
		}

		// get current version
		$current_version = $this->getVersion();

		$notifications = array();

		// find and run the next update
		foreach ($this->getRequiredUpdates() as $version) {
			if ((version_compare($version, $current_version) > 0)) {
				$class = 'Update'.str_replace('.', '', $version);
				$this->app->loader->register($class, "updates:$version.php");

				if (class_exists($class)) {

					// make sure class implemnts iUpdate interface
					$r = new ReflectionClass($class);
					if ($r->isSubclassOf('iUpdate') && !$r->isAbstract()) {

						// run the update
						$notification = $r->newInstance()->getNotifications($this->app);
						if (is_array($notification)) {
							$notifications = array_merge($notifications, $notification);
						}
					}
				}
			}
		}

		return $notifications;
	}

	/**
	 * Performs the next update.
	 *
	 * @return array response
	 * @since 2.0
	 */
	public function run() {

		// check if update is required
		if (!$this->required()) {
			return $this->_createResponse('No update required.', false, false);
		}

		// get current version
		$current_version = $this->getVersion();

		// find and run the next update
		$updates = $this->getRequiredUpdates();
		foreach ($updates as $version) {
			if ((version_compare($version, $current_version) > 0)) {
				$class = 'Update'.str_replace('.', '', $version);
				$this->app->loader->register($class, "updates:$version.php");

				if (class_exists($class)) {

					// make sure class implemnts iUpdate interface
					$r = new ReflectionClass($class);
					if ($r->isSubclassOf('iUpdate') && !$r->isAbstract()) {

						try {

							// run the update
							$r->newInstance()->run($this->app);
						} catch (Exception $e) {

							return $this->_createResponse("Error during update! ($e)", true, false);
						}

						// set current version
						$version_string = $version;
						if (!$required = count($updates) > 1) {
							if (($xml = simplexml_load_file($this->app->path->path('component.admin:zoo.xml'))) && (string) $xml->name == 'ZOO') {
								$version_string = (string) $xml->version;
							}
						}
						$this->setVersion($version);
						return $this->_createResponse('Successfully updated to version '.$version_string, false, $required);
					}
				}
			}
		}

		return $this->_createResponse('No update found.', false, false);
	}

	/**
	 * Drops and recreates all ZOO database table indexes.
	 *
	 * @since 2.0
	 */
	public function refreshDBTableIndexes() {

		// sanatize table indexes
		if ($this->app->path->path('component.admin:installation/index.sql')) {

			$db = $this->app->database;

			// read index.sql
			$buffer = JFile::read($this->app->path->path('component.admin:installation/index.sql'));

			// Create an array of queries from the sql file
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (!empty($queries)) {

				foreach ($queries as $query) {

					// replace table prefixes
					$query = $db->replacePrefix($query);

					// parse table name
					preg_match('/ALTER\s*TABLE\s*`(.*)`/i', $query, $result);

					if (count($result) < 2) {
						continue;
					}

					$table = $result[1];

					// check if table exists
					if (!$db->queryResult('SHOW TABLES LIKE '.$db->Quote($table))) {
						continue;
					}

					// get existing indexes
					$indexes = $db->queryObjectList('SHOW INDEX FROM '.$table);

					// drop existing indexes
					$removed = array();
					foreach ($indexes as $index) {
						if (in_array($index->Key_name, $removed)) {
							continue;
						}
						if ($index->Key_name != 'PRIMARY') {
							$db->query('DROP INDEX '.$index->Key_name.' ON '.$table);
							$removed[] = $index->Key_name;
						}
					}

					// add new indexes
					$db->query($query);
				}
			}
		}
	}

	/**
	 * Gets the current version from versions table.
	 *
	 * @return string version
	 * @since 2.0
	 */
	public function getVersion() {

		$cache = $this->getCache();
		if (!$version = $cache->get('zoo_version')) {
			// make sure versions table is present
			$this->app->database->query('CREATE TABLE IF NOT EXISTS '.ZOO_TABLE_VERSION.' (version varchar(255) NOT NULL) ENGINE=MyISAM;');

			$version = $this->app->database->queryResult('SELECT version FROM '.ZOO_TABLE_VERSION);
		}
		$cache->set('zoo_version', $version)->save();
		return $version;

	}

	/**
	 * Writes the current version in versions table.
	 *
	 * @param string $version
	 *
	 * @since 2.0
	 */
	public function setVersion($version) {

		// remove previous versions
		$this->app->database->query('TRUNCATE TABLE '.ZOO_TABLE_VERSION);

		// set version
		$this->app->database->query('INSERT INTO '.ZOO_TABLE_VERSION.' SET version='.$this->app->database->Quote($version));

		$this->getCache()->clear()->save();
	}

	/**
	 * Creates an response
	 *
	 * @param string $message
	 * @param string $error
	 * @param boolean $continue
	 *
	 * @return array response
	 * @since 2.0
	 */
	protected function _createResponse($message, $error, $continue) {
		$message = JText::_($message);
		return compact('message', 'error', 'continue');
	}

	/**
	 * Outputs a message if there is a new update available.
	 *
	 * @since 2.0
	 */
	public function available() {

		// check for updates
		if ($xml = @simplexml_load_file($this->app->path->path('component.admin:zoo.xml'))) {

			// update check
			if ($url = (string) current($xml->xpath('//updateUrl'))) {

				// create check url
				$url = sprintf('%s?application=%s&version=%s&format=raw', $url, 'zoo_j17', urlencode(current($xml->xpath('//version'))));

				// only check once a day
				$hash = md5($url.date('Y-m-d'));

				$cache = $this->getCache();
				$check = $cache->get('check');
				$data  = $cache->get('data');
				$prev_message = @$data['message'];

				if ($check != $hash) {
					if ($request = $this->app->http->get($url)) {
						$check = $hash;
						$data = json_decode($request['body'], true);
					}
					if ($prev_message != @$data['message']) {
						$cache->set('hideUpdateNotification', false);
					}
				}

				// decode response and set message
				if (!$cache->get('hideUpdateNotification') && @$data['status'] == 'update-available') {
					$close = $this->app->joomla->version->isCompatible('3.2') ?
						'<script type="text/javascript">jQuery(function($) {$(\'#system-message-container [data-dismiss]\').bind(\'click\', function() { $.ajax(\'%s\') }); });</script>'
						: '<span onclick="jQuery.ajax(\'%s\'); jQuery(this).closest(\'ul\').hide();" class="hide-update-notification"></span>';

					$close = sprintf($close, $this->app->link(array('controller' => 'manager', 'task' => 'hideUpdateNotification', 'format' => 'raw'), false));

					$this->app->system->application->enqueueMessage(@$data['message'].$close, 'notice');
				}

				$cache->set('check', $check)->set('data', $data)->save();
			}
		}
	}

	/**
	 * Hides the update notifications for this session
	 *
	 * @since 2.0
	 */
	public function hideUpdateNotification() {
		$this->getCache()->set('hideUpdateNotification', true)->save();
	}

	/**
	 * Returns cache
	 *
	 * @return AppCache The update cache
	 * @since 2.0
	 */
	protected function getCache() {
		if (empty($this->_cache)) {
			$this->_cache = $this->app->cache->create($this->app->path->path('cache:') . '/zoo_update_cache');
			if (!$this->_cache->check()) {
				$this->app->system->application->enqueueMessage('Cache not writeable please update the file permissions!', 'warning');
			}
		}
		return $this->_cache;

	}

}

/**
 * Update interface
 *
 * @package Component.Helpers
 * @since 2.0
 */
interface iUpdate {

	/**
	 * Get preupdate notifications.
	 *
	 * @param Application $app The application to get the notifications from
	 * @return array messages
	 * @since 2.0
	 */
	public function getNotifications($app);

	/**
	 * Performs the update.
	 *
	 * @param Application $app The application to get the notifications from
	 * @return boolean true if updated successful
	 * @since 2.0
	 */
	public function run($app);
}

/**
 * UpdateAppException identifies an Exception in the UpdateHelper class
 * @see UpdateHelper
 */
class UpdateAppException extends AppException {}