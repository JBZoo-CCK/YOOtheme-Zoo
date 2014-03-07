<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Modifications helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ModificationHelper extends AppHelper {

	/**
	 * Verifies the ZOO installation.
	 *
	 * @return boolean true if ZOO hasn't been modified
	 * @since 2.0
	 */
	public function verify() {
		$result = $this->check();
		return empty($result);
	}

	/**
	 * Checks for ZOO modifications.
	 *
	 * @return array modified files
	 * @throws AppModificationException
	 * @since 2.0
	 */
	public function check() {

		if (!$checksum = $this->app->path->path('component.admin:checksums')) {
			throw new AppModificationException(JText::_('Unable to locate checksums file in ' . $this->app->path->path('component.admin:')));
		}

		$path = $this->app->path->path('component.admin:');
		$this->app->checksum->verify($path, $checksum, $result, array(
			create_function('$path', 'return (in_array($path, array("zoo.xml", "file.script.php")) ? "admin/" . $path : $path);'),
			create_function('$path', 'if (preg_match("#^admin#", $path)) return preg_replace("#^admin/#", "", $path);'),
		), $this->app->path->relative($path).'/');

		$path = $this->app->path->path('component.site:');
		$this->app->checksum->verify($path, $checksum, $result, array(
			create_function('$path', 'if (preg_match("#^site#", $path)) return preg_replace("#^site/#", "", $path);')
		), $this->app->path->relative($path).'/');

		return $result;
	}

	/**
	 * Cleans any modifications from the filessystem.
	 *
	 * @return boolean true on success
	 * @since 2.0
	 */
	public function clean() {

		// check for modifications
		$results = $this->check();
		if (isset($results['unknown'])) {
			foreach ($results['unknown'] as $file) {
				if (!empty($file) && ($file = $this->app->path->path('root:'.$file))) {
					// remove unknown file
					if (!JFile::delete($file)) {
						$this->app->error->raiseWarning(0, sprintf(JText::_('Could not remove file (%s)'), $file));
					}
				}
			}
		}

		return true;

	}

}

/**
 * AppModificationException identifies an Exception in the ModificationHelper class
 * @see ModificationHelper
 */
class AppModificationException extends AppException {}