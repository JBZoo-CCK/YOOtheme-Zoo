<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The general helper class for template
 *
 * @package Component.Helpers
 * @since 2.0
 */
class TemplateHelper extends AppHelper {

	/**
	 * Class constructor
	 *
	 * @param App $app The app instance
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppTemplate', 'classes:template.php');
	}

	/**
	 * Get a template instance
	 *
	 * @param array $args Additional constructor arguments
	 *
	 * @return AppTemplate The template
	 */
	public function create($args = array()) {
		$args = (array) $args;
		return $this->app->object->create('AppTemplate', $args);
	}

}