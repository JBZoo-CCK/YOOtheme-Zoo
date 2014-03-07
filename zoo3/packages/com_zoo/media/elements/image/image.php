<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementImage
		The image element class
*/
class ElementImage extends Element implements iSubmittable {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$file = $this->get('file');
		return !empty($file) && JFile::exists(JPATH_ROOT.'/'.$this->get('file'));
	}

	/*
		Function: getSearchData
			Get elements search data.

		Returns:
			String - Search data
	*/
	public function getSearchData() {
		return $this->get('title');
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// init vars
		$params = $this->app->data->create($params);
		$title  = $this->get('title');
		$file  	= $this->app->zoo->resizeImage(JPATH_ROOT.'/'.$this->get('file'), $params->get('width', 0), $params->get('height', 0));
		$link   = JURI::root() . $this->app->path->relative($file);

		$url = $target = $rel = '';
		if ($params->get('link_to_item', false)) {

            if ($this->getItem()->getState()) {

                $url   = $this->app->route->item($this->_item);
                $title = empty($title) ? $this->_item->name : $title;

            }

		} else if ($this->get('link')) {

			$url 	= $this->get('link');
			$target	= $this->get('target');
			$rel  	= $this->get('rel');

		}

		// get alt
		$alt = empty($title) ? $this->_item->name : $title;

		// render layout
		if ($file && $layout = $this->getLayout()) {
			return $this->renderLayout($layout,
				compact('file', 'title', 'alt', 'link', 'params', 'url', 'target', 'rel')
			);
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

		$this->app->document->addScript('assets:js/image.js');

        if ($layout = $this->getLayout('edit.php')) {
            return $this->renderLayout($layout);
        }

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

		// load js
		$this->app->document->addScript('elements:image/image.js');

        // init vars
        $image        = $this->get('file');

        // is uploaded file
        $image        = is_array($image) ? '' : $image;

        // get params
        $trusted_mode = $params->get('trusted_mode');

        // build image select
        $lists = array();
        if ($trusted_mode) {
            $options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Image').' -'));
            if (!empty($image) && !$this->_inUploadPath($image)) {
                $options[] = $this->app->html->_('select.option', $image, '- '.JText::_('No Change').' -');
            }
            $img_ext = str_replace(',', '|', trim(JComponentHelper::getParams('com_media')->get('image_extensions'), ','));
			foreach ($this->app->path->files('root:'.$this->_getUploadImagePath(), false, '/\.('.$img_ext.')$/i') as $file) {
                $options[] = $this->app->html->_('select.option', $this->_getUploadImagePath().'/'.$file, $file);
            }
            $lists['image_select'] = $this->app->html->_('select.genericlist', $options, $this->getControlName('image'), 'class="image"', 'value', 'text', $image);
        } else {
            if (!empty($image)) {
                $image = $this->app->zoo->resizeImage($this->app->path->path('root:' . $image), 0, 0);
                $image = $this->app->path->relative($image);
            }
        }

        if (!empty($image)) {
            $image = $this->app->path->url('root:' . $image);
        }

        if ($layout = $this->getLayout('submission.php')) {
            return $this->renderLayout($layout,
				compact('lists', 'image', 'trusted_mode')
			);
        }

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

        // init vars
        $trusted_mode = $params->get('trusted_mode');

        // get old file value
        $old_file = $this->get('file');

        $file = '';
        // get file from select list
        if ($trusted_mode && $file = $value->get('image')) {

            if (!$this->_inUploadPath($file) && $file != $old_file) {
                throw new AppValidatorException(sprintf('This file is not located in the upload directory.'));
            }

            if (!JFile::exists($file)) {
                throw new AppValidatorException(sprintf('This file does not exist.'));
            }

        // get file from upload
        } else {

            try {

                // get the uploaded file information
                $userfile = $value->get('userfile', null);

				$max_upload_size = $this->config->get('max_upload_size', '512') * 1024;
				$max_upload_size = empty($max_upload_size) ? null : $max_upload_size;
                $file = $this->app->validator
						->create('file', array('mime_type_group' => 'image', 'max_size' => $max_upload_size))
						->addMessage('mime_type_group', 'Uploaded file is not an image.')
						->clean($userfile);

            } catch (AppValidatorException $e) {
                if ($e->getCode() != UPLOAD_ERR_NO_FILE) {
                    throw $e;
                }

                if (!$trusted_mode && $old_file && $value->get('image')) {
                    $file = $old_file;
                }

            }

        }

        if ($params->get('required') && empty($file)) {
            throw new AppValidatorException('Please select an image to upload.');
        }

		$result = compact('file');

		if ($trusted_mode) {
			$result['title'] = $this->app->validator->create('string', array('required' => false))->clean($value->get('title'));
			$result['link'] = $this->app->validator->create('url', array('required' => false), array('required' => 'Please enter an URL.'))->clean($value->get('link'));
			$result['target'] = $this->app->validator->create('', array('required' => false))->clean($value->get('target'));
			$result['rel'] = $this->app->validator->create('string', array('required' => false))->clean($value->get('rel'));
		}

		// connect to submission beforesave event
		$this->app->event->dispatcher->connect('submission:beforesave', array($this, 'submissionBeforeSave'));

		return $result;
	}

    protected function _inUploadPath($image) {
        return $this->_getUploadImagePath() == dirname($image);
    }

    protected function _getUploadImagePath() {
		return trim(trim($this->config->get('upload_directory', 'images/zoo/uploads/')), '\/');
    }

	/*
		Function: submissionBeforeSave
			Callback before item submission is saved

		Returns:
			void
	*/
    public function submissionBeforeSave() {

        // get the uploaded file information
        if (($userfile = $this->get('file')) && is_array($userfile)) {
            // get file name
            $ext = $this->app->filesystem->getExtension($userfile['name']);
            $base_path = JPATH_ROOT . '/' . $this->_getUploadImagePath() . '/';
            $file = $base_path . $userfile['name'];
            $filename = basename($file, '.'.$ext);

            $i = 1;
            while (JFile::exists($file)) {
                $file = $base_path . $filename . '-' . $i++ . '.' . $ext;
            }

            if (!JFile::upload($userfile['tmp_name'], $file)) {
                throw new AppException('Unable to upload file.');
            }

			$this->app->zoo->putIndexFile(dirname($file));

            $this->set('file', $this->app->path->relative($file));
        }
    }

	/*
		Function: bindData
			Set data through data array.

		Parameters:
			$data - array

		Returns:
			Void
	*/
	public function bindData($data = array()) {
		parent::bindData($data);

		// add image width/height
		$file = $this->get('file');
		if ($file && $filepath = $this->app->path->path('root:'.$file)) {
			$size = getimagesize($filepath);
			$this->set('width', ($size ? $size[0] : 0));
			$this->set('height', ($size ? $size[1] : 0));
		}
	}

}