<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Field renderer helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class FieldHelper extends AppHelper  {

	/**
	 * Class constructor
	 *
	 * @param string $app App instance.
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// register paths
		$this->app->path->register($this->app->path->path('helpers:fields'), 'fields');
	}

	/**
	 * Render a field like text, select or radio button
	 *
	 * @param string $type The type of the field
	 * @param string $name The name of the field
	 * @param mixed $value The value of the field
	 * @param object $node The node
	 * @param array $args The arguments to pass on to the field layout
	 *
	 * @return string The html output
	 *
	 * @since 2.0
	 */
	public function render($type, $name, $value, $node, $args = array()) {

		if (empty($type)) return;

		// set vars
		$args['name']  = $name;
		$args['value'] = $value;
		$args['node']  = $node;

		$__file = $this->app->path->path("fields:$type.php");

		if ($__file != false) {
			// render the field
			extract($args);
			ob_start();
			include($__file);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		return 'Field Layout "'.$type.'" not found. ('.$this->app->utility->debugInfo(debug_backtrace()).')';

	}

	/**
	 * Create html attribute string from array
	 *
	 * @param array $attributes The attributes
	 * @param array $ignore The attributes to ignore
	 *
	 * @return string the attribute string
	 *
	 * @since 2.0
	 */
	public function attributes($attributes, $ignore = array()) {

		$attribs = array();
		$ignore  = (array) $ignore;

		foreach ($attributes as $name => $value) {
			if (in_array($name, $ignore)) continue;

			$attribs[] = sprintf('%s="%s"', $name, htmlspecialchars($value));
		}

		return implode(' ', $attribs);
	}

}