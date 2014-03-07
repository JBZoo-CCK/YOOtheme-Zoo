<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper to deal with Joomla versions and basic configuration
 *
 * @package Framework.Helpers
 */
class JoomlaHelper extends AppHelper {

	/**
	 * The current joomla version
	 *
	 * @var JVersion
	 * @since 1.0.0
	 */
	public $version;

	/**
	 * Class Constructor
	 *
	 * @param App $app A reference to the global app object
	 */
	public function __construct($app) {
		parent::__construct($app);

		JLoader::import('joomla.version');

		$this->version = new JVersion();
	}

	/**
	 * Get the current Joomla installation short version (i.e: 2.5.3)
	 *
	 * @return string The short version of joomla (ie: 2.5.3)
	 *
	 * @since 1.0.0
	 */
	public function getVersion() {
		return $this->version->getShortVersion();
	}

	/**
	 * Check the current version of Joomla
	 *
	 * @param string $version The version to check
	 * @param boolean $release Compare only release versions (2.5 vs 2.5 even if 2.5.6 != 2.5.3)
	 *
	 * @return boolean If the version of Joomla is equal of the one passed
	 *
	 * @since 1.0.0
	 */
	public function isVersion($version, $release = true) {
		return $release ? $this->version->RELEASE == $version : $this->getVersion() == $version;
	}

	/**
	 * Get the default access group
	 *
	 * @return int The default group id
	 *
	 * @since 1.0.0
	 */
	public function getDefaultAccess() {
		return $this->app->system->config->get('access');
	}

	/**
	 * Check if the version is joomla 1.5
	 *
	 * @deprecated 2.5 Use JoomlaHelper::isVersion() instead
	 *
	 * @return boolean If is joomla 1.5
	 *
	 * @since 1.0.0
	 */
	public function isJoomla15() {
		return $this->isVersion('1.5');
	}

}
