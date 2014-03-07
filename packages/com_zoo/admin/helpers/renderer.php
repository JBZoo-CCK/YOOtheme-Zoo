<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Renderer helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class RendererHelper extends AppHelper {

	/**
	 * Class constructor
	 *
	 * @param App $app The app instance
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// register paths
		$this->app->path->register($this->app->path->path('classes:renderer'), 'renderer');
	}

	/**
	 * Creates a Renderer instance
	 *
	 * @param string $type Renderer type
	 * @param array $args Additional arguments for the constructor
	 * @return AppRenderer
	 * @since 2.0
	 */
	public function create($type = '', $args = array()) {

		// load renderer class
		$class = $type ? $type.'Renderer' : 'AppRenderer';
		if ($type) {
			$this->app->loader->register($class, 'renderer:'.strtolower($type).'.php');
		}

		// prepend app
		array_unshift($args, $this->app);

		return $this->app->object->create($class, $args);
	}

}

/**
 * The general class for rendering objects.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class AppRenderer {

	/**
	 * The path helper
	 * @var PathHelper
	 */
	protected $_path;

	/**
	 * The layout paths
	 * @var array
	 */
	protected $_layout_paths;

	/**
	 * The current layout
	 * @var string
	 */
	protected $_layout;

	/**
	 * The base folder
	 * @var string
	 */
	protected $_folder = 'renderer';

	/**
	 * The separator for layout paths
	 * @var string
	 */
	protected $_separator = '.';

	/**
	 * The extension type of the layouts
	 * @var string
	 */
	protected $_extension = '.php';

	/**
	 * The metafile name
	 * @var string
	 */
	protected $_metafile = 'metadata.xml';

	/**
	 * App instance
	 *
	 * @var App
	 * @since 2.0
	 */
	public $app;

	/**
	 * Maximum recursion depth
	 */

	const MAX_RENDER_RECURSIONS = 100;

	/**
	 * Class constructor
	 *
	 * @param App $app The app instance
	 * @param PathHelper|null $path The path helper to use
	 * @since 2.0
	 */
	public function __construct($app, $path = null) {
		$this->_layout_paths = array();
		$this->_path = $path ? $path : $app->object->create('PathHelper', array($app));
	}

	/**
	 * Render objects using a layout file.
	 *
	 * @staticvar int $count
	 *
	 * @param string $layout Layout name.
	 * @param array $args Arguments to be passed to into the layout scope.
	 *
	 * @return string The rendered output
	 * @since 2.0
	 */
	public function render($layout, $args = array()) {

		// prevent render to recurse indefinitely
		static $count = 0;
		$count++;

		if ($count < self::MAX_RENDER_RECURSIONS) {

			// render layout
			if ($__layout = $this->_getLayoutPath($layout)) {

				// import vars and layout output
				extract($args);
				ob_start();
				include($__layout);
				$output = ob_get_contents();
				ob_end_clean();

				$count--;

				return $output;
			}

			$count--;

			// raise warning, if layout was not found
			JError::raiseWarning(0, 'Renderer Layout "'.$layout.'" not found. ('.$this->app->utility->debugInfo(debug_backtrace()).')');

			return null;
		}

		// raise warning, if render recurses indefinitly
		JError::raiseWarning(0, 'Warning! Render recursed indefinitly. ('.$this->app->utility->debugInfo(debug_backtrace()).')');

		return null;
	}

	/**
	 * Gets the layout path for the given layout
	 *
	 * @param string $layout
	 *
	 * @return string The layout path
	 * @since 2.0
	 */
	protected function _getLayoutPath($layout) {

		if (!isset($this->_layout_paths[$layout])) {
			// init vars
			$parts = explode($this->_separator, $layout);
			$this->_layout = preg_replace('/[^A-Z0-9_\.-]/i', '', end($parts));
			$this->_layout_paths[$layout] = $this->_path->path(implode('/', $parts).$this->_extension);
		}

		return $this->_layout_paths[$layout];
	}

	/**
	 * Add layout path(s) to renderer.
	 *
	 * @param array|string $paths
	 *
	 * @return self
	 * @since 2.0
	 */
	public function addPath($paths) {

		$paths = (array) $paths;

		foreach ($paths as $path) {
			$path = rtrim($path, "\\/").'/';
			$this->_path->register($path.$this->_folder);
		}

		return $this;
	}

	/**
	 * Retrieve an array of layout filenames.
	 *
	 * @param string $dir
	 * @return array the layout files
	 * @since 2.0
	 */
	public function getLayouts($dir) {

		// find layouts in path(s)
		$layouts = $this->_path->files($dir, false, '/'.preg_quote($this->_extension).'$/i');

		return array_map(create_function('$layout', 'return basename($layout, "'.$this->_extension.'");'), $layouts);
	}

	/**
	 * Retrieve metadata array of a layout.
	 *
	 * @param string $layout
	 *
	 * @return array The layouts metadata
	 * @since 2.0
	 */
	public function getLayoutMetaData($layout) {

		// init vars
		$metadata = $this->app->object->create('AppData');
		$parts = explode($this->_separator, $layout);
		$name = array_pop($parts);

		if ($file = $this->_path->path(implode(DIRECTORY_SEPARATOR, $parts).'/'.$this->_metafile)) {
			if ($xml = simplexml_load_file($file)) {
				foreach ($xml->children() as $child) {
					$attributes = $child->attributes();
					if ($child->getName() == 'layout' && (string) $attributes->name == $name) {

						foreach ($attributes as $key => $attribute) {
							$metadata[$key] = (string) $attribute;
						}

						$metadata['layout'] = $layout;
						$metadata['name'] = (string) $child->name;
						$metadata['description'] = (string) $child->description;

						break;
					}
				}
			}
		}

		return $metadata;
	}

	/**
	 * Retrieve the renderers folder.
	 *
	 * @return string The folder
	 * @since 2.0
	 */
	public function getFolder() {
		return $this->_folder;
	}

	/**
	 * Retrieve paths where to find the layout files.
	 *
	 * @param string $dir
	 * @return string The full path
	 * @since 2.0
	 */
	protected function _getPath($dir = '') {
		return $this->_path->path($dir);
	}

}

/**
 * The base class for rendering positions based on config files.
 *
 * @package Component.Helpers
 * @since 2.0
 */
abstract class PositionRenderer extends AppRenderer {

	/**
	 * The config data
	 * @var AppParameter
	 */
	protected $_config;

	/**
	 * The config file
	 * @var string
	 */
	protected $_config_file = 'positions.config';

	/**
	 * The positions file
	 * @var string
	 */
	protected $_xml_file = 'positions.xml';

	/**
	 * Retrieve positions of a layout.
	 *
	 * @param string $dir point separated path to layout, last part is layout
	 *
	 * @return array The positions array
	 * @since 2.0
	 */
	public function getPositions($dir) {

		// init vars
		$positions = array();

		$parts = explode('.', $dir);
		$layout = array_pop($parts);
		$path = implode('/', $parts);

		// parse positions xml
		if ($xml = simplexml_load_file($this->_getPath($path.'/'.$this->_xml_file))) {
            if ($pos = current($xml->xpath('positions[@layout="'.$layout.'"]'))) {

                $positions['name'] = ($name = current($pos->xpath('name')) ? (string) $name : $layout);
                $positions['positions'] = array();

                foreach ($pos->xpath('position[@name]') as $position) {
                    $name = (string) $position->attributes()->name;
                    $positions['positions'][$name] = (string) $position;
                }
			}
		}

		return $positions;
	}

	/**
	 * Retrieve position configuration.
	 *
	 * @param string $dir path to config file
	 *
	 * @return AppParameter
	 * @since 2.0
	 */
	public function getConfig($dir) {

		// config file
		if (empty($this->_config)) {

			if ($file = $this->_path->path($dir.'/'.$this->_config_file)) {
				$content = file_get_contents($file);
			} else {
				$content = null;
			}

			$this->_config = $this->app->parameter->create($content);
		}

		return $this->_config;
	}

	/**
	 * Save position configuration.
	 *
	 * @param AppParameter|string $config Configuration
	 * @param string $file File to save configuration
	 *
	 * @return boolean
	 *
	 * @throws PositionRendererException
	 * @since 2.0
	 */
	public function saveConfig($config, $file) {

		if (JFile::exists($file) && !is_writable($file)) {
			throw new PositionRendererException(sprintf('The config file is not writable (%s)', $file));
		}

		if (!JFile::exists($file) && !is_writable(dirname($file))) {
			throw new PositionRendererException(sprintf('Could not create config file (%s)', $file));
		}

		// Joomla 1.6 JFile::write expects $buffer to be reference
		$config_string = (string) $config;
		return JFile::write($file, $config_string);
	}

	/**
	 * Checks if a path exists
	 *
	 * @param string $dir The path to check
	 * @return boolean
	 * @since 2.0
	 */
	public function pathExists($dir) {
		return (bool) $this->_getPath($dir);
	}

	/**
	 * Check if any of the positions from a layout generates some output
	 *
	 * @param string $dir Point separated path to layout, last part is layout
     * @param Item $item The Item to be checked (default: null)
	 *
	 * @return boolean If any of the positions generates some kind of output
	 *
	 * @since 3.0.4
	 */
	public function checkPositions($dir, $item = null) {

		$positions = $this->getPositions($dir);
		if (isset($positions['positions']) && is_array($positions['positions'])) {

			// set item
			$this->_item = isset($this->_item) ? $this->_item : $item;

			// set layout
			if (!isset($this->_layout)) {
				$parts = explode('.', $dir);
				$this->_layout = array_pop($parts);
			}

			// proceede with checking
			foreach ($positions['positions'] as $position => $title) {
				if ($this->checkPosition($position)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Check if a position generates some output
	 *
	 * @param string $position The name of the position to check
	 *
	 * @return boolean If the position generates some kind of output
	 *
	 * @since 2.0
	 */
	public abstract function checkPosition($position);

	/**
	 * Render the output of the position
	 *
	 * @param string $position The name of the position to render
	 * @param array $args The list of arguments to pass on to the layout
	 *
	 * @return string The html code generated
	 *
	 * @since 2.0
	 */
	public abstract function renderPosition($position, $args = array());

}

/**
 * PositionRendererException identifies an Exception in the PositionRenderer class
 * @see PositionRenderer
 */
class PositionRendererException extends AppException {}