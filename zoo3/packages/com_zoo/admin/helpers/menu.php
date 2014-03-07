<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Menu helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class MenuHelper extends AppHelper {

	/**
	 * The menus
	 * @var array
	 */
	protected static $_menus = array();

	/**
	 * The active site menu item
	 * @var array
	 */
	protected $_active;

	/**
	 * Class constructor
	 *
	 * @param string $app App instance.
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppTree', 'classes:tree.php');
		$this->app->loader->register('AppMenu', 'classes:menu.php');
	}

	/**
	 * Gets the AppMenu instance
	 *
	 * @param string $name Menu name
	 * @return AppMenu
	 * @since 2.0
	 */
	public function get($name) {

		if (isset(self::$_menus[$name])) {
			return self::$_menus[$name];
		}

		self::$_menus[$name] = $this->app->object->create('AppMenu', array($name));

		return self::$_menus[$name];
	}

	/**
	 * Gets the active site menu
	 */
	public function getActive() {
		if ($this->_active === null) {
			if ($menu = $this->app->system->application->getMenu('site') and $menu instanceof JMenu) {
				$this->_active = $menu->getActive();
			}
		}
		return $this->_active;
	}

}