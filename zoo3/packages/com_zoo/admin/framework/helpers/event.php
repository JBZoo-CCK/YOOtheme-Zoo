<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper to register and create AppEvent objects
 * 
 * @package Framework.Helpers
 */
class EventHelper extends AppHelper {

	/**
	 * The dispatcher object
	 * 
	 * @var AppEventDispatcher
	 * @since 1.0.0
	 */
	protected static $_dispatcher;

	/**
	 * Class Constructor
	 * 
	 * @param App Reference to the global App object
	 */
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppEvent', 'classes:event.php');
		$this->app->loader->register('AppEventDispatcher', 'classes:event.php');

		// set dispatcher
		if (!isset(self::$_dispatcher)) {
			self::$_dispatcher = new AppEventDispatcher();
		}

	}

	/**
	 * Register an Event class
	 * 
	 * @param string $class The classname to load
	 * @param string $file The path to the file. By default it searches in events: path
	 * 
	 * @since 1.0.0
	 */
	public function register($class, $file = null) {
		
		if ($file == null) {
			$file = 'events:'.basename(strtolower($class), 'event').'.php';
		}
	
		return $this->app->loader->register($class, $file);
	}

 	/**
	 * Create a new AppEvent object
	 * 
	 * @param mixed $subject The subject of the event
	 * @param string $name The event name
	 * @param array $parameters The parameters for the event
	 * 
	 * @return AppEvent The AppEvent object
	 * 
	 * @since 1.0.0
	 */
	public static function create($subject, $name, $parameters = array()) {
		return new AppEvent($subject, $name, $parameters);
	}
	
	/**
	 * Get variables from the AppEventDispatcher object
	 * 
	 * @param string $name The name of the property to get
	 * 
	 * @return mixed The property value
	 * 
	 * @since 1.0.0
	 */
	public function __get($name) {
		
		if ($name == 'dispatcher') {
			return self::$_dispatcher;
		}
		
		return null;
	}
	
}