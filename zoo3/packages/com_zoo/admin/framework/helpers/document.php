<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper that acts as a wrapper for JDocument
 *
 * @package Framework.Helpers
 */
class DocumentHelper extends AppHelper {

	/**
	 * Last modification date for the file. Used for prevent the browser to use the cached version of an older CSS file
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $file_mod_date;

	/**
	 * Adds a CSS to the document head
	 *
	 * @param string $path The path to the css file
	 * @param string $version A version to add to the url to prevent caching (default: last modification date of the file)
	 *
	 * @since 1.0.0
	 */
	public function addStylesheet($path, $version = null) {
		if ($file = $this->app->path->url($path)) {
			$this->app->system->document->addStylesheet($file.$this->getVersion($version));
		}
	}

	/**
	 * Adds a javascript file to the document head
	 *
	 * If jquery wasn't loaded yet, load it before the other javascript files
	 *
	 * @param string $path The path to the javascript file
	 * @param string $version A version to add to the url to prevent caching (default: last modification date of the file)
	 *
	 * @since 1.0.0
	 */
	public function addScript($path, $version = null) {

		$version = $this->getVersion($version);

		// load jQuery, if not loaded before
		if (!$this->app->joomla->version->isCompatible('3.0')) {
			if (!$this->app->system->application->get('jquery')) {
				$this->app->system->application->set('jquery', true);
				$this->app->system->document->addScript($this->app->path->url('libraries:jquery/jquery.js').$version);
			}
		} else {
			JHtml::_('jquery.framework');
		}

		if ($file = $this->app->path->url($path)) {
			$this->app->system->document->addScript($file.$version);
		}
	}

	/**
	 * Magic method to map JDocument methods to this helper
	 *
	 * @param string $method The method name
	 * @param array $args The list of arguments to pass on to the method
	 *
	 * @return mixed The result of the called method
	 *
	 * @since 1.0.0
	 */
    public function __call($method, $args) {
		return $this->_call(array($this->app->system->document, $method), $args);
    }

	/**
	 * Get the get parameter to append using the version of a file.
	 *
	 * If no version is found, check the last time the file was edited and use that date as the version
	 *
	 * @param string $version The version to append (default: last modification date)
	 *
	 * @return string The get parameter to append
	 */
	private function getVersion($version = null) {

		if ($version === null) {
			if (empty($this->file_mod_date)) {
				$this->file_mod_date = date("Ymd", filemtime(__FILE__));
			}

			return '?ver='.$this->file_mod_date;
		}

		return empty($version) ? '' : '?ver='.$version;
	}

}