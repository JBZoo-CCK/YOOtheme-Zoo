<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementFile
		The file element class
*/
abstract class ElementFile extends Element {

	protected $_extensions = '';

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();

		// set defaults
		$params = JComponentHelper::getParams('com_media');
		$this->config->set('directory', $params->get('file_path'));
	}

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		// init vars
		$file = $this->app->path->path('root:'.$this->get('file'));
		return !empty($file) && is_readable($file) && is_file($file);
	}

	/*
		Function: getDirectory
			Returns the directory with trailing slash.

		Returns:
			String - directory
	*/
	public function getDirectory() {
		return rtrim($this->config->get('directory'), '/').'/';
	}

	/*
	   Function: getExtension
	       Get the file extension string.

	   Returns:
	       String - file extension
	*/
	public function getExtension() {
		return $this->app->filesystem->getExtension($this->get('file'));
	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		parent::loadAssets();
		$this->app->document->addScript('assets:js/finder.js');
	}

	/*
		Function: files
			Get directory/file list JSON formatted

		Returns:
			Void
	*/
	public function files() {
		$files = array();
		$path = ltrim($this->app->request->get('path', 'string'), '/');
		$path = empty($path) ? '' : $path.'/';
		foreach ($this->app->path->dirs('root:'.$this->getDirectory().$path) as $dir) {
			$files[] = array('name' => basename($dir), 'path' => $path.$dir, 'type' => 'folder');
		}
		foreach ($this->app->path->files('root:'.$this->getDirectory().$path, false, '/^.*('.$this->_extensions.')$/i') as $file) {
			$files[] = array('name' => basename($file), 'path' => $this->getDirectory().$path.$file, 'type' => 'file');
		}

		return json_encode($files);
	}

}