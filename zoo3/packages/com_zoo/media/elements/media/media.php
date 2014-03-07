<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register ElementFile class
App::getInstance('zoo')->loader->register('ElementFile', 'elements:file/file.php');

/*
	Class: ElementMedia
		The video element class
*/
class ElementMedia extends ElementFile implements iSubmittable {

	protected $_extensions = 'mp4|webm|flv|swf|wmv|mp3';
	protected $_youtube_regex = '/(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/';
	protected $_youtubeshort_regex = '/(\/\/.*?youtu\.be)\/([^\?]+)(.*)/i';
	protected $_vimeo_regex = '/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/';

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();

		// set callbacks
		if ($this->app->system->application->isAdmin()) {
			$this->registerCallback('files');
		}
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
		$url  = $this->get('url');
		return parent::hasValue($params) || !empty($url);
	}

	/*
		Function: getVideoFormat
			Trys to return the video format for source.

	   Parameters:
            $source - the video source

		Returns:
			String - the video format, if found
	*/
	public function getVideoFormat($source) {

		if (preg_match($this->_youtube_regex, $source)) {
			return 'youtube';
		} else if (preg_match($this->_youtubeshort_regex, $source)) {
			return 'youtu.be';
		} else if (preg_match($this->_vimeo_regex, $source)) {
			return 'vimeo';
		} else if (($ext = $this->app->filesystem->getExtension($source)) && in_array($ext, explode('|', $this->_extensions))) {
			return strtolower($ext);
		}

		return null;
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
		$width    = $this->get('width', $this->config->get('defaultwidth'));
		$height   = $this->get('height', $this->config->get('defaultheight'));
		$autoplay = $this->get('autoplay', $this->config->get('defaultautoplay', false));
		$source   = $this->get('file') ? $this->app->path->url('root:'.$this->get('file')) : $this->get('url');

		if ($format = $this->getVideoFormat($source)) {

			$width_attr = $width ? ' width="'.$width.'"' : '';
			$height_attr = $height && $format != 'mp3' ?  ' height="'.$height.'"' : '';

			switch ($format) {
				case 'vimeo':

					$source = preg_replace($this->_vimeo_regex, '//player.vimeo.com/video/$2', $source);
					break;

				case 'youtube':

					$source = rtrim(preg_replace($this->_youtube_regex, '//www.youtube.com/embed/$2?$3', $source), '?');
					break;

				case 'youtu.be':

					$source = rtrim(preg_replace($this->_youtubeshort_regex, '//www.youtube.com/embed/$2$3', $source), '?');
					break;

				case 'swf':

					$this->app->document->addScript('elements:media/assets/js/swfobject.js');
					$width    = $width ? $width : 200;
					$height   = $height ? $height : 200;
					$autoplay = $autoplay ? 'true' : 'false';
					$id		  = 'swf-'.uniqid();

					return "<div id=\"$id\">
								<p><a href=\"http://www.adobe.com/go/getflashplayer\"><img src=\"http://www.adobe.com/images/shared/download_buttons/get_adobe_flash_player.png\" alt=\"Get Adobe Flash player\" /></a></p>
							</div>
							<script type=\"text/javascript\">
								swfobject.embedSWF(\"$source\", \"$id\", \"$width\", \"$height\", \"7.0.0\", \"\", {}, {allowFullScreen:\"true\", wmode: \"transparent\", play:\"$autoplay\" });
							</script>";

				default:

					$pluginPath = $this->app->path->url('elements:media/assets/mediaelement/')."/";
					$options = compact('pluginPath');

					// add stylesheets/javascripts
					$this->app->document->addScript('elements:media/assets/mediaelement/mediaelement-and-player.min.js');
					$this->app->document->addStylesheet('elements:media/assets/mediaelement/mediaelementplayer.min.css');
					$this->app->document->addScriptDeclaration(sprintf("
						jQuery(function($) {
							mejs.MediaElementDefaults.pluginPath='".$pluginPath."';
							$('.element-media video, .element-media audio').each(function() {
								var ele = $(this);

								if(!ele.parent().hasClass('mejs-mediaelement')) {

									ele.data('mediaelement', new mejs.MediaElementPlayer(this, %s));

									var w = ele.data('mediaelement').width, h = ele.data('mediaelement').height;

									$.onMediaQuery('(max-width: 767px)', {
										valid: function() {
											ele.data('mediaelement').setPlayerSize('100%%', ele.is('video') ? '100%%':h);
										},
										invalid: function() {
											var parent_width = ele.parent().width();

											if (w > parent_width) {
												ele.css({width:'',height:''}).data('mediaelement').setPlayerSize('100%%', '100%%');
											} else {
												ele.css({width:'',height:''}).data('mediaelement').setPlayerSize(w, h);
											}
										}
									});

									if ($(window).width() <= 767) {
										ele.data('mediaelement').setPlayerSize('100%%', ele.is('video') ? '100%%':h);
									}

								}
							});
						});", count($options) ? json_encode($options) : '{}'));

					$autoplay = $autoplay ? ' autoplay="autoplay"' : '';
					$tag	  = $format == 'mp3' ? 'audio' : 'video';
					$type	  = " type=\"$tag/$format\"";

					return '<'.$tag.' src="'.$source.'"'.$width_attr.$height_attr.$autoplay.$type.'></'.$tag.'>';

			}

			$this->app->document->addScriptDeclaration(
				"jQuery(function($) {
					$.onMediaQuery('(max-width: 767px)', {
						valid: function() {
							$('.element-media iframe').attr('width', 'auto').attr('height', 'auto');
						},
						invalid: function() {
							$('.element-media iframe').attr('width', '$width').attr('height', '$height');
						}
					});
				});");

			$autoplay = $autoplay && !preg_match('/autoplay=/', $source)? (strpos($source, '?') === false ? '?' : '&').'autoplay=1' : '';
			$wmode = !preg_match('/wmode=/', $source) ? (!$autoplay && strpos($source, '?') === false ? '?' : '&').'wmode=transparent' : '';
			return '<iframe src="'.$source.$autoplay.$wmode.'"'.$width_attr.$height_attr.'></iframe>';

		}

		return JText::_('No video selected.');

	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		parent::loadAssets();
		$this->app->document->addScript('elements:media/assets/js/media.js');
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->renderLayout($this->getLayout('edit.php'));
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
		return $this->renderLayout($this->getLayout('submission.php'), (array) $params);
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

		$url = (string) $this->app->validator->create('url', array('required' => $params->get('required')), array('required' => 'Please enter an URL.'))->clean($value->get('url'));

		if ($url && (!$format = $this->getVideoFormat($url))) {
			throw new AppValidatorException('Not a valid video format.');
        }

		$width     = (string) $this->app->validator->create('integer', array('required' => false), array('number' => 'The Width needs to be a number.'))->clean($value->get('width', $this->config->get('defaultwidth')));
		$height    = (string) $this->app->validator->create('integer', array('required' => false), array('number' => 'The Height needs to be a number.'))->clean($value->get('height', $this->config->get('defaultheight')));
		$autoplay  = (bool) $params->get('trusted_mode') ? $value->get('autoplay') : $this->config->get('defaultautoplay', false);

		return compact('url', 'format', 'width', 'height', 'autoplay');
	}

}