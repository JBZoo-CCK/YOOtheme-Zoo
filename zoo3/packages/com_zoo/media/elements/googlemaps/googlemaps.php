<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementGooglemaps
		The google maps element class
*/
class ElementGooglemaps extends Element implements iSubmittable {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$value = $this->get('location');
		return !empty($value);
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
		$params			   = $this->app->data->create($params);
		$location          = $this->get('location');
		$locale            = $this->config->get('locale');
		$key			   = $this->config->get('key');

		// init display params
		$layout   		   = $params->get('layout');
		$width             = $params->get('width');
		$width_unit        = $params->get('width_unit');
		$height            = $params->get('height');
		$information       = $params->get('information');

		// determine locale
		if (empty($locale) || $locale == 'auto') {
			$locale = $this->app->user->getBrowserDefaultLanguage();
		}

		// get marker text
		$marker_text = '';
		$renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->_item->getApplication()->getTemplate()->getPath()));
		if ($item = $this->getItem()) {
			$path   = 'item';
			$prefix = 'item.';
			$type   = $item->getType()->id;
			if ($renderer->pathExists($path.DIRECTORY_SEPARATOR.$type)) {
				$path   .= DIRECTORY_SEPARATOR.$type;
				$prefix .= $type.'.';
			}

			if (in_array($layout, $renderer->getLayouts($path))) {
				$marker_text = $renderer->render($prefix.$layout, array('item' => $item));
			} else {
				$marker_text = $item->name;
			}
		}

		// get geocode cache
		$cache = $this->app->cache->create($this->app->path->path('cache:') . '/geocode_cache');
		if (!$cache->check()) {
			$this->app->system->application->enqueueMessage(sprintf('Cache not writable please update the file permissions! (%s)', $this->app->path->path('cache:') . '/geocode_cache'), 'notice');
			return;
		}

		// get map center coordinates
		try {

			$center = $this->app->googlemaps->locate($location, $cache);

		} catch (GooglemapsHelperException $e) {
			$this->app->system->application->enqueueMessage($e, 'notice');
			return;
		}

		// save location to geocode cache
		if ($cache) $cache->save();

		// add assets
		$this->app->document->addStylesheet('elements:googlemaps/googlemaps.css');

		// css parameters
		$maps_id           = 'googlemaps-'.uniqid();
		$css_module_width  = 'width: '.$width.$width_unit.';';
		$css_module_height = 'height: '.$height.'px;';
		$data = json_encode(array(
			'lat' => $center['lat'],
			'lng' => $center['lng'],
			'popup' => (boolean) $params->get('marker_popup'),
			'text' => $this->app->googlemaps->stripText($marker_text),
			'zoom' => (int) $params->get('zoom_level'),
			'mapCtrl' => $params->get('map_controls'),
			'zoomWhl' => (boolean) $params->get('scroll_wheel_zoom'),
			'mapType' => $params->get('map_type'),
			'typeCtrl' => (boolean) $params->get('type_controls'),
			'directions' => (boolean) $params->get('directions'),
			'locale' => $locale,
			'mainIcon' => $params->get('main_icon'),
			'msgFromAddress' => JText::_('From address:'),
			'msgGetDirections' => JText::_('Get directions'),
			'msgEmpty' => JText::_('Please fill in your address.'),
			'msgNotFound' => JText::_('SORRY, ADDRESS NOT FOUND'),
			'msgAddressNotFound' => ', ' . JText::_('NOT FOUND')
		));


		// js parameters
		$javascript = "jQuery(function($) { $('#$maps_id').Googlemaps({$data}); });";

		// render layout
		if ($layout = $this->getLayout()) {
			return $this->renderLayout($layout, compact('maps_id', 'javascript', 'css_module_width', 'css_module_height', 'information', 'locale', 'key'));
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
		$locale = $this->config->get('locale');
		$key	= $this->config->get('key');

		$this->app->system->document->addScript("http://maps.google.com/maps/api/js?sensor=false&language=$locale&key=$key&libraries=places");
		$this->app->document->addScript('elements:googlemaps/jquery.geocomplete.js');
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
        if ($layout = $this->getLayout('edit.php')) {
            return $this->renderLayout($layout);
        }

        return null;
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
        return $this->edit();
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
        $validator = $this->app->validator->create('textfilter', array('required' => $params->get('required')), array('required' => 'Please enter a location'));
        $clean = $validator->clean($value->get('location'));
		return array('location' => $clean);
	}

    public function geocode() {

        if (!$location = $this->get('location')) {
            return;
        }

        // get geocode cache
        $cache = $this->app->cache->create($this->app->path->path('cache:') . '/geocode_cache');
        if (!$cache->check()) {
            return;
        }

        // get map center coordinates
        try {

            $this->app->googlemaps->locate($location, $cache);

        } catch (GooglemapsHelperException $e) {
            return;
        }

        // save location to geocode cache
        if ($cache) $cache->save();
    }

}