<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Install helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class InstallHelper extends AppHelper {

	/**
	 * Uninstall Application
	 *
	 * @param Application $application
	 * @since 2.0
	 * @throws InstallHelperException
	 */
	public function uninstallApplication(Application $application) {

		$group = $application->getGroup();

		if ($this->_applicationExists($group)) {
			throw new InstallHelperException('Delete existing applications first.');
		}

		if (!($directory = $this->app->path->path("applications:$group")) || !JFolder::delete($directory)) {
			throw new InstallHelperException('Unable to delete directory: (' . $directory . ')');
		}

	}

	/**
	 * Checks if application exists
	 *
	 * @param string $group
	 * @return boolean
	 * @since 2.0
	 */
	protected function _applicationExists($group) {
		$result = $this->app->table->application->first(array('conditions' => 'application_group = '.$this->app->database->Quote($group)));
		return !empty($result);
	}

	/**
	 * Installs an Application from a user upload.
	 *
	 * @param array $userfile The userfile to install from
	 * @return int 2 = update, 1 = install
	 *
	 * @throws InstallHelperException
	 * @since 2.0
	 */
	public function installApplicationFromUserfile($userfile) {
		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			throw new InstallHelperException('Fileuploads are not enabled in php.');
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			throw new InstallHelperException('No file selected.');
		}

		// Check if there was a problem uploading the file.
		if ($userfile['error'] || $userfile['size'] < 1) {
			throw new InstallHelperException('Upload error occured.');
		}

		// Temporary folder to extract the archive into
		$tmp_directory = $this->app->path->path('tmp:').'/';
		$archivename = $tmp_directory.$userfile['name'];

		if (!JFile::upload($userfile['tmp_name'], $archivename)) {
			throw new InstallHelperException("Could not move uploaded file to ($archivename)");
		}

		// Clean the paths to use for archive extraction
		$extractdir = $tmp_directory.uniqid('install_');

		jimport('joomla.filesystem.archive');

		// do the unpacking of the archive
		if (!JArchive::extract($archivename, $extractdir)) {
			throw new InstallHelperException("Could not extract zip file to ($tmp_directory)");
		}

		return $this->installApplicationFromFolder($extractdir);

	}

	/**
	 * Installs an Application from a folder.
	 *
	 * @param string $folder The application folder
	 *
	 * @return int 2 = update, 1 = install
	 *
	 * @throws InstallHelperException
	 * @since 2.0
	 */
	public function installApplicationFromFolder($folder) {
		$folder = rtrim($folder, "\\/") . '/';

		if (!($manifest = $this->findManifest($folder))) {
			throw new InstallHelperException('No valid xml file found in the directory');
		}

		$group = $this->getGroup($manifest);
		if (empty($group)) {
			throw new InstallHelperException('No app group in application.xml specified.');
		}

		$update = false;

		$write_directory = $this->app->path->path('applications:').'/'.$group.'/';
		if (JFolder::exists($write_directory)) {

			$files = $this->app->filesystem->readDirectoryFiles($folder.'types/', '', '/\.config$/', false);
			foreach ($files as $file) {
				if (JFile::exists($write_directory.'types/'.$file)) {
					JFile::delete($folder.'types/'.$file);
				}
			}

			$files = $this->app->filesystem->readDirectoryFiles($folder, '', '/(positions\.(config|xml)|metadata\.xml)$/', true);
			foreach ($files as $file) {
				if (JFile::exists($write_directory.$file)) {
					JFile::delete($folder.$file);
				}
			}

			$update = true;
		}

		if (!JFolder::copy($folder, $write_directory, '', true)) {
			throw new InstallHelperException('Unable to write to folder: ' . $write_directory);
		}

		$applications = $this->app->application->groups();
		$application = isset($applications[$group]) ? $applications[$group] : null;

		// trigger installed event
		$this->app->event->dispatcher->notify($this->app->event->create($application, 'application:installed', compact('update')));

		return $update ? 2 : 1;
	}

	/**
	 * Reads application group from manifest
	 *
	 * @param SimpleXMLElement $manifest
	 * @return string group
	 * @since 2.0
	 */
	public function getGroup(SimpleXMLElement $manifest) {
		return (string) $manifest->group;
	}

	/**
	 * Reads version from manifest
	 *
	 * @param SimpleXMLElement $manifest
	 * @return string version
	 * @since 2.0
	 */
	public function getVersion(SimpleXMLElement $manifest) {
		return (string) $manifest->version;
	}

	/**
	 * Finds manifest in path
	 *
	 * @param string $path
	 * @return SimpleXMLElement|false
	 * @since 2.0
	 */
	public function findManifest($path) {
		$path = rtrim($path, "\\/") . '/';
		foreach ($this->app->filesystem->readDirectoryFiles($path, $path, '/\.xml$/', false) as $file) {
			if (($xml = simplexml_load_file($file)) && $this->isManifest($xml)) {
				return $xml;
			}
		}

		return false;

	}

	/**
	 * Checks if xml is manifest
	 *
	 * @param SimpleXMLElement $xml
	 * 
	 * @return boolean
	 * 
	 * @since 2.0
	 */
	public function isManifest(SimpleXMLElement $xml) {
		return $xml->getName() == 'application';
	}

}

/**
 * InstallHelperException identifies an Exception in the InstallHelper class
 * @see InstallHelper
 */
class InstallHelperException extends AppException {}