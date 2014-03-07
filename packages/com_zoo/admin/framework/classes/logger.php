<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Logger class
 *
 * @abstract
 * @package Framework.Classes
 */
abstract class AppLogger {

	const LEVEL_SUCCESS = 0;
	const LEVEL_INFO    = 1;
	const LEVEL_NOTICE  = 2;
	const LEVEL_WARNING = 3;
	const LEVEL_ERROR   = 4;
	const LEVEL_DEBUG   = 5;

	/**
	 * Reference to the global App object
	 *
	 * @var App
	 * @since 1.0.0
	 */
	public $app;

	/**
	 * The log levels
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $_level = array();

	/**
	 * Class constructor
	 */
	public function __construct() {

		// init vars
		$this->_level = array(self::LEVEL_SUCCESS, self::LEVEL_INFO, self::LEVEL_NOTICE, self::LEVEL_WARNING, self::LEVEL_ERROR);

	}

	/**
	 * General log method
	 *
	 * @param int $level The log level
	 * @param string $message The log message
	 * @param string $type The log type
	 *
	 * @since 1.0.0
	 */
	public function log($level, $message, $type = null) {

		if (in_array($level, $this->_level)) {
			$this->_log($level, $message, $type);
		}

	}

	/**
	 * Create a success log
	 *
	 * @param string $message The log message
	 * @param string $type The log type
	 *
	 * @since 1.0.0
	 */
	public function success($message, $type = null) {
		$this->log(self::LEVEL_SUCCESS, $message, $type);
	}

	/**
	 * Create an info log
	 *
	 * @param string $message The log message
	 * @param string $type The log type
	 *
	 * @since 1.0.0
	 */
	public function info($message, $type = null) {
		$this->log(self::LEVEL_INFO, $message, $type);
	}

	/**
	 * Create a notice log
	 *
	 * @param string $message The log message
	 * @param string $type The log type
	 *
	 * @since 1.0.0
	 */
	public function notice($message, $type = null) {
		$this->log(self::LEVEL_NOTICE, $message, $type);
	}

	/**
	 * Create a warning log
	 *
	 * @param string $message The log message
	 * @param string $type The log type
	 *
	 * @since 1.0.0
	 */
	public function warning($message, $type = null) {
		$this->log(self::LEVEL_WARNING, $message, $type);
	}

	/**
	 * Create an error log
	 *
	 * @param string $message The log message
	 * @param string $type The log type
	 *
	 * @since 1.0.0
	 */
	public function error($message, $type = null) {
		$this->log(self::LEVEL_ERROR, $message, $type);
	}

	/**
	 * Create a debug log
	 *
	 * @param string $message The log message
	 * @param string $type The log type
	 *
	 * @since 1.0.0
	 */
	public function debug($message, $type = null) {
		$this->log(self::LEVEL_DEBUG, $message, $type);
	}

	/**
	 * Event listener callback
	 *
	 * @param array $event An array containing the event message, level and type
	 *
	 * @since 1.0.0
	 */
	public function listen($event) {
		$this->log($event['level'], $event['message'], $event['type'] != null ? $event['type'] : 'main');
	}


	/**
	 * Get the current log levels
	 *
	 * @return array The list of levels
	 *
	 * @since 1.0.0
	 */
	public function getLogLevel() {
		return $this->_level;
	}

	/**
	 * Set the log levels
	 *
	 * @param int|array $level The log level(s)
	 *
	 * @since 1.0.0
	 */
	public function setLogLevel($level) {
		$this->_level = (array) $level;
	}

	/**
	 * Get the logger level text
	 *
	 * @param int $level The level to get the text for
	 *
	 * @return string The level text
	 *
	 * @since 1.0.0
	 */
	public function getLevelText($level) {

		$levels[self::LEVEL_SUCCESS] = JText::_('Success');
		$levels[self::LEVEL_INFO] = JText::_('Info');
		$levels[self::LEVEL_NOTICE] = JText::_('Notice');
		$levels[self::LEVEL_WARNING] = JText::_('Warning');
		$levels[self::LEVEL_ERROR] = JText::_('Error');
		$levels[self::LEVEL_DEBUG] = JText::_('Debug');

		return isset($levels[$level]) ? $levels[$level] : JText::_('Unknown');
	}

	/**
	 * Log method
	 *
	 * @abstract
	 *
	 * @param int $level The log level
	 * @param string $message The log Message
	 * @param string $type The log type
	 *
	 * @since 1.0.0
	 */
	abstract protected function _log($level, $message, $type = null);

}