<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Archive helper class
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ArchiveHelper extends AppHelper {

	/**
	 * Class constructor
	 *
	 * @param string $app App instance.
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// increase memory limit
		@ini_set('memory_limit', '256M');

		$app->loader->register('PclZip', 'libraries:pcl/pclzip.lib.php');
	}

	/**
	 * Open an archive file, works for zip archives only
	 *
	 * @param string $file The full file path to open
	 * @param string $format The file format. If not specified, will be guessed by the file extension (currently works for zip archives only)
	 *
	 * @return PclZip
	 *
	 * @since 2.0
	 */
	public function open($file, $format = 'zip') {

		// auto-detect format
		if (!$format) {
			$format = $this->format($file);
		}

		// create archive object
		if ($format == 'zip') {
			return new PclZip($file);
		}

		return null;
	}

	/**
	 * Get the archive format based on the file extension
	 *
	 * Any tar-related format (tgz, gz, etc) will be returned as tar
	 *
	 * @param string $file The filename or the full file path
	 *
	 * @return string The file format (zip or tar)
	 *
	 * @since 2.0
	 */
	public function format($file) {

		// detect .zip format
		if (preg_match('/\.zip$/i', $file)) {
			return 'zip';
		}

		// detect .tar format
		if (preg_match('/\.tar$|\.tar\.gz$|\.tgz$|\.tar\.bz2$|\.tbz2$/i', $file)) {
			return 'tar';
		}

		return null;
	}

}