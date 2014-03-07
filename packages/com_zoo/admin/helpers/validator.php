<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Validator helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ValidatorHelper extends AppHelper {

	/**
	 * Class constructor
	 *
	 * @param App $app The app instance
	 *
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppValidator', 'classes:validator.php');
	}

	/**
	 * Creates a validator instance
	 *
	 * @param string $type Validator type
	 *
	 * @return AppValidator
	 */
	public function create($type = '') {

		$args = func_get_args();
		$type = array_shift($args);

		$class = 'AppValidator'.$type;

		// load class
		$this->app->loader->register($class, 'classes:validator.php');
		return $this->app->object->create($class, $args);
	}

}