<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Log helper class
 * 
 * @package Framework.Helpers
 */
class LogHelper extends AppHelper {

	/**
	 * The event to trigger when loggin
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $_event = 'app:log';

	/**
	 * Class Constructor
	 * 
	 * @param App $app A reference to the global App object
	 */
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppLogger', 'classes:logger.php');
	}

	/**
	 * Log a success message
	 * 
	 * @param string $message The message to log
	 * @param string $type The type of the message
	 * 
	 * @see _notify
	 * 
	 * @since 1.0.0
	 */
	public function success($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_SUCCESS, $message, $type);
	}

	/**
	 * Log an info message
	 * 
	 * @param string $message The message to log
	 * @param string $type The type of the message
	 * 
	 * @see _notify
	 * 
	 * @since 1.0.0
	 */
	public function info($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_INFO, $message, $type);
	}

	/**
	 * Log a notice message
	 * 
	 * @param string $message The message to log
	 * @param string $type The type of the message
	 * 
	 * @see _notify
	 * 
	 * @since 1.0.0
	 */
	public function notice($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_NOTICE, $message, $type);
	}

	/**
	 * Log a warning message
	 * 
	 * @param string $message The message to log
	 * @param string $type The type of the message
	 * 
	 * @see _notify
	 * 
	 * @since 1.0.0
	 */
	public function warning($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_WARNING, $message, $type);
	}

	/**
	 * Log an error message
	 * 
	 * @param string $message The message to log
	 * @param string $type The type of the message
	 * 
	 * @see _notify
	 * 
	 * @since 1.0.0
	 */
	public function error($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_ERROR, $message, $type);
	}

	/**
	 * Log a debug message
	 * 
	 * @param string $message The message to log
	 * @param string $type The type of the message
	 * 
	 * @see _notify
	 * 
	 * @since 1.0.0
	 */
	public function debug($message, $type = null) {
		$this->_notify(AppLogger::LEVEL_DEBUG, $message, $type);
	}

	/**
	 * Create a logger object
	 * 
	 * @param string $type The type of logger to create
	 * @param array $args The parameters to pass to the logger class
	 * 
	 * @return AppLogger The logger object
	 * 
	 * @since 1.0.0
	 */
	public function createLogger($type, $args = array()) {
		
		// load data class
		$class = $type.'Logger';
		$this->app->loader->register($class, 'loggers:'.strtolower($type).'.php');

		// use reflection for logger creation
		if (count($args) > 0) {
			$reflection = new ReflectionClass($class);
			$logger = $reflection->newInstanceArgs($args);
		} else {
			$logger = new $class();
		}

		return $this->addLogger($logger);
	}

	/**
	 * Connect the logger object to the log event
	 * 
	 * @param AppLogger $logger The logger object
	 * 
	 * @return AppLogger The logger object
	 * 
	 * @since 1.0.0
	 */
	public function addLogger($logger) {
		
		// set app
		$logger->app = $this->app;
		
		// add logger to application log event
		$this->app->event->dispatcher->connect($this->_event, array($logger, 'listen'));

		return $logger;
	}

	/**
	 * Disconnect the logger object from the event
	 * 
	 * @param AppLogger $logger The logger object
	 * 
	 * @return AppLogger The logger object
	 * 
	 * @since 1.0.0
	 */
	public function removeLogger($logger) {
		
		// remove logger from application log event
		$this->app->event->dispatcher->disconnect($this->_event, array($logger, 'listen'));

		return $logger;
	}

	/**
	 * Trigger the event with the log message
	 * 
	 * @param int $level The level of the log
	 * @param string $message The log message
	 * @param string $type The type of the log
	 * 
	 * @since 1.0.0
	 */
	protected function _notify($level, $message, $type = null) {

		// auto-detect type
		if ($type == null) {
			
			// get backtrace
			$backtrace = debug_backtrace();
			if (isset($backtrace[2]['class'])) {
				$type = $backtrace[2]['class'];
			} elseif (isset($backtrace[2]['object'])) {
				$type = get_class($backtrace[2]['object']);
			}

		}

		// fire event
	    $this->app->event->dispatcher->notify($this->app->event->create($this, $this->_event, compact('level', 'message', 'type')));
		
	}

}