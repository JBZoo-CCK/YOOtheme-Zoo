<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementGallery
		The file element class
*/
class ElementGallery extends Element implements iSubmittable{

	protected $filter = "/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/i";

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();

		if ($this->app->system->application->isAdmin()) {
			// set callbacks
			$this->registerCallback('dirs');
		}

		// connect to submission beforesubmissiondisplay event
		$this->app->event->dispatcher->connect('element:beforesubmissiondisplay', array($this, 'showInSubmission'));

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

	public function getResource() {
		return 'root:'.$this->getDirectory().trim($this->get('value'), '/');
	}

	public function getFiles() {
		return $this->app->path->files($this->getResource(), false, $this->filter);
	}

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - AppData render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		// init vars
		$params = $this->app->data->create($params);
		$value  = $this->get('value');
		if (empty($value)) {
			return false;
		}
		$thumbs = $this->_getThumbnails($params);
		return !empty($thumbs);
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - AppData render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// init vars
		$params = $this->app->data->create($params);

		// get thumbnails
		$thumbs = $this->_getThumbnails($params);

		// no thumbnails found
		if (empty($thumbs)) {
			return JText::_('No thumbnails found');
		}

		// limit thumbnails to count
		if (($count = (int) $params->get('count', 0)) && $count < count($thumbs)) {
			$thumbs = array_slice($thumbs, 0, $count);
		}

		// add css and javascript
		$this->app->document->addScript('elements:gallery/gallery.js');
		$this->app->document->addStylesheet('elements:gallery/gallery.css');

		// spotlight
		if ($params->get('effect') == 'spotlight') {
			$this->app->document->addScript('assets:js/spotlight.js');
			$this->app->document->addStylesheet('assets:css/spotlight.css');
			$this->app->document->addScriptDeclaration("jQuery(function($) { $('.zoo-gallery [data-spotlight]').spotlight(); });");
		}

		if ($layout = $this->getLayout($params->get('mode', 'lightbox').'.php')) {
			return $this->renderLayout($layout, compact('thumbs', 'params'));
		}

		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		// init vars
		$title = htmlspecialchars(html_entity_decode($this->get('title'), ENT_QUOTES), ENT_QUOTES);

		if ($layout = $this->getLayout('edit.php')) {
            return $this->renderLayout($layout, compact('title'));
        }

        return null;

	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		$this->app->document->addScript('assets:js/finder.js');
		$this->app->document->addScript('elements:gallery/gallery.js');
	}

	/*
		Function: dirs
			Get directory list JSON formatted

		Returns:
			Void
	*/
	public function dirs() {
		$dirs = array();
		$path = $this->app->request->get('path', 'string');
		foreach ($this->app->path->dirs('root:'.$this->getDirectory().$path) as $dir) {
			$count = count($this->app->path->files('root:'.$this->getDirectory().$path.'/'.$dir, false, $this->filter));
			$dirs[] = array('name' => basename($dir) . " ($count)", 'path' => (!empty($path) ? $path.'/' : '').$dir, 'type' => 'folder');
		}

		return json_encode($dirs);
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {

        if ($params->get('trusted_mode', false)) {
			echo $this->app->html->_('control.selectdirectory', 'root:'.$this->_getDirectoryPath(), null, $this->getControlName('value'), $this->get('value'));
		}

		return false;
	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {

		$value = $value->get('value', '');

		if ($params->get('required') && empty($value)) {
            throw new AppValidatorException('Please select a gallery folder.');
        }

		if (!empty($value) && !$this->_inDirectoryPath($value)) {
			throw new AppValidatorException(sprintf('This file is not located in the upload directory.'));
		}

		return compact('value');
	}

	protected function _getThumbnails($params) {

		$thumbs     = array();
		$width      = (int) $params->get('width');
		$height     = (int) $params->get('height');
		$title      = $this->get('title', '');
		$path		= $this->app->path->path($this->getResource()).'/';

		// set default thumbnail size, if incorrect sizes defined
		if ($width < 1 && $height < 1) {
			$width  = 100;
			$height = null;
		}

		foreach ($this->getFiles() as $filename) {

			$file  = $path.$filename;
			$thumb = $this->app->zoo->resizeImage($file, $width, $height);

			// if thumbnail exists, add it to return value
			if (is_file($thumb)) {

				// set image name or title if exsist
				$name = !empty($title) ? $title : $this->app->string->ucwords($this->app->string->str_ireplace('_', ' ', JFile::stripExt($filename)));

				// get image info
				list($thumb_width, $thumb_height) = @getimagesize($thumb);

				$thumbs[] = array(
					'name'         => $name,
					'filename'     => $filename,
					'img'          => JURI::root().$this->app->path->relative($file),
					'img_file'     => $file,
					'thumb'		   => JURI::root().$this->app->path->relative($thumb),
					'thumb_width'  => $thumb_width,
					'thumb_height' => $thumb_height
				);
			}
		}

		usort($thumbs, create_function('$a,$b', 'return strcmp($a["filename"], $b["filename"]);'));
		switch ($params->get('order', 'asc')) {
			case 'random':
				shuffle($thumbs);
				break;
			case 'desc':
				$thumbs = array_reverse($thumbs);
				break;
		}

		return $thumbs;
	}

	protected function _inDirectoryPath($folder) {
		return (bool) $this->app->path->path('root:'.$this->_getDirectoryPath().'/'.$folder);
    }

    protected function _getDirectoryPath() {
		return trim(trim($this->config->get('directory', 'images/')), '\/');
    }

	public function showInSubmission($event) {
		if ($event['element']->identifier == $this->identifier) {
			$event['render'] = (bool) $event['params']['trusted_mode'];
		}
	}

}