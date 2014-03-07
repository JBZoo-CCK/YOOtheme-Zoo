<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper for managing and retrieving paths
 *
 * @package Framework.Helpers
 */
class PathHelper extends AppHelper {

	/**
	 * A list of registered paths
	 *
	 * @var array
	 * @since 1.0.0
	 */
    protected $_paths = array();

	/**
	 * Register a path to a namespace
	 *
	 * @param string $path The path to register
	 * @param string $namespace The namespace to register the path to
	 *
	 * @since 1.0.0
	 */
	public function register($path, $namespace = 'default') {

	    if (!isset($this->_paths[$namespace])) {
	        $this->_paths[$namespace] = array();
	    }

	    array_unshift($this->_paths[$namespace], $path);
	}

	/**
	 * Get an absolute path to a file or a directory
	 *
	 * @param string $resource The resource with a namespace (ie: "assets:js/app.js")
	 *
	 * @return array|string The path(s) to the resource
	 *
	 * @since 1.0.0
	 */
	public function path($resource) {

		// parse resource
		extract($this->_parse($resource));

		return $this->_find($paths, $path);
	}

	/**
	 * Get all absolute paths registered to a file or a directory
	 *
	 * @param string $resource The resource with a namespace (ex: "assets:js/app.js")
	 *
	 * @return array The list of paths
	 *
	 * @since 1.0.0
	 */
	public function paths($resource) {

		// parse resource
		extract($this->_parse($resource));

		return $paths;
	}

	/**
	 * Get the absolute url to a file
	 *
	 * @param string $resource The resource with a namespace (ex: "assets:js/app.js")
	 *
	 * @return string The absolute url
	 *
	 * @since 1.0.0
	 */
	public function url($resource) {

		// init vars
	    $parts = explode('?', $resource);
	    $url   = str_replace(DIRECTORY_SEPARATOR, '/', $this->path($parts[0]));

	    if ($url) {

	        if (isset($parts[1])) {
	            $url .= '?'.$parts[1];
	        }

	        $url = JURI::root(true).'/'.$this->relative($url);
	    }

	    return $url;
	}

	/**
	 * Get a list of files from a resource
	 *
	 * @param string $resource The resource with a namespace (ex: "assets:js/")
	 * @param boolean $recursive If the search should be recursive (default: false)
	 * @param string $filter A regex filter for the search
	 *
	 * @return array The list of files
	 *
	 * @since 1.0.0
	 */
	public function files($resource, $recursive = false, $filter = null) {
		return $this->ls($resource, 'file', $recursive, $filter);
	}

	/**
	 * Get a list of directories from a resource
	 *
	 * @param string $resource The resource with a namespace (ex: "assets:js/")
	 * @param boolean $recursive If the search should be recursive (default: false)
	 * @param string $filter A regex filter for the search
	 *
	 * @return array The list of directories
	 *
	 * @since 1.0.0
	 */
	public function dirs($resource, $recursive = false, $filter = null) {
		return $this->ls($resource, 'dir', $recursive, $filter);
	}

	/**
	 * Get a list of files or diretories from a resource
	 *
	 * @param string $resource The resource with a namespace (ex: "assets:js/")
	 * @param string $mode Can be 'file' or 'dir'.
	 * @param boolean $recursive If the search should be recursive (default: false)
	 * @param string $filter A regex filter for the search
	 *
	 * @return array The list of files or directories
	 *
	 * @since 1.0.0
	 */
	public function ls($resource, $mode = 'file', $recursive = false, $filter = null) {

		$files = array();
		$res   = $this->_parse($resource);

		foreach ($res['paths'] as $path) {
			if (file_exists($path.'/'.$res['path'])) {
				foreach ($this->_list(realpath($path.'/'.$res['path']), '', $mode, $recursive, $filter) as $file) {
					if (!in_array($file, $files)) {
						$files[] = $file;
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Parse a resource string
	 *
	 * @param string $resource The resource with a namespace (ex: "assets:js/")
	 *
	 * @return array An associative array containing "namespace", "paths", "path"
	 *
	 * @since 1.0.0
	 */
	protected function _parse($resource) {

	    // init vars
		$parts     = explode(':', $resource, 2);
		$count     = count($parts);
		$path      = '';
		$namespace = 'default';

		// parse resource path
		if ($count == 1) {
			list($path) = $parts;
		} elseif ($count == 2) {
			list($namespace, $path) = $parts;
		}

		// remove heading slash or backslash
		$path = ltrim($path, "\\/");

	    // get paths for namespace, if exists
		$paths = isset($this->_paths[$namespace]) ? $this->_paths[$namespace] : array();

		return compact('namespace', 'paths', 'path');
    }

	/**
	 * Find a file or a directory in the given paths
	 *
	 * @param array $paths The paths to search in
	 * @param string $file The file or directory to search for
	 *
	 * @return string The path to the file/directory or false if no resource was found
	 *
	 * @since 1.0.0
	 */
	protected function _find($paths, $file) {

		$paths = (array) $paths;
		$file  = ltrim($file, "\\/");

		foreach ($paths as $path) {
			if ($fullpath = realpath("$path/$file") and file_exists($fullpath) and (stripos($fullpath, JPATH_ROOT, 0) === 0 || JPATH_ROOT === '')) {
				return $fullpath;
			}
		}

		return false;
	}

	/**
	 * Get the list of files or directories in a given path
	 *
	 * @param string $path The path to search in
	 * @param string $prefix A prefix to prepend
	 * @param string $mode Can mode 'file' or 'dir'
	 * @param boolean $recursive If the search should be recursive (default: false)
	 * @param string $filter A regex filter to use
	 *
	 * @return array A list of files or directories
	 *
	 * @since 1.0.0
	 */
	protected function _list($path, $prefix = '', $mode = 'file', $recursive = false, $filter = null) {

		$files  = array();
	    $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

		if ($scan = @scandir($path)) {
			foreach ($scan as $file) {

				// continue if ignore match
				if (in_array($file, $ignore)) {
					continue;
				}

	            if (is_dir($path.'/'.$file)) {

					// add dir
					if ($mode == 'dir') {

						// continue if no regex filter match
						if ($filter && !preg_match($filter, $file)) {
							continue;
						}

						$files[] = $prefix.$file;
					}

					// continue if not recursive
					if (!$recursive) {
						continue;
					}

					// read subdirectory
	            	$files = array_merge($files, $this->_list($path.'/'.$file, $prefix.$file.'/', $mode, $recursive, $filter));

				} else {

					// add file
					if ($mode == 'file') {

						// continue if no regex filter match
						if ($filter && !preg_match($filter, $file)) {
							continue;
						}

						$files[] = $prefix.$file;
					}

	            }

			}
		}

		return $files;
	}

	/**
	 * Makes a path relative to the Joomla root directory
	 *
	 * @param string $path The absolute path
	 *
	 * @return string The relative path
	 *
	 * @since 1.0.0
	 */
	public function relative($path) {
		return ltrim(preg_replace('/^'.preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT), '/').'/i', '', str_replace(DIRECTORY_SEPARATOR, '/', $path)), '/');
	}

}