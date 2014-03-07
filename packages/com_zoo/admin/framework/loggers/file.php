<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/
/**
 * File logger
 * 
 * @package Framework.Loggers
 * 
 * @see AppLogger
 */
class FileLogger extends AppLogger {

	/**
	 * Format of the log message
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $_format;
	
	/**
	 * The timestamp format
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $_time;
	
	/**
	 * Path to the file to user for loggin
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $_file;

	/**
	 * The file resource
	 * 
	 * @var resource
	 * @since 1.0.0
	 */
	protected $_fp;

	/**
	 * Class constructor
	 * 
	 * @param string $file The path to the file to use for loggin
	 * @param string $format The format of the log file
	 * @param strign $time The timestamp format
	 */
	public function __construct($file, $format = '%time% %level% %type% %message%%EOL%', $time = '%b %d %H:%M:%S') {
		parent::__construct();

		// init vars
		$this->_format = $format;
		$this->_time = $time;
		$this->_file = $file;
		$this->_fp = fopen($file, 'a');

	}

	/**
	 * Write a log entry
	 * 
	 * @param string $level The level of the log
	 * @param string $message The message
	 * @param string $type The type of the log
	 * 
	 * @since 1.0.0
	 */
    protected function _log($level, $message, $type = null) {
	    flock($this->_fp, LOCK_EX);

	    fwrite($this->_fp, strtr($this->_format, array(
	      '%time%'    => strftime($this->_time),
	      '%level%'   => str_pad('['.$this->getLevelText($level).']', 10),
	      '%type%'    => '{'.$type.'}',
	      '%message%' => $message,
	      '%EOL%'     => PHP_EOL,
	    )));

	    flock($this->_fp, LOCK_UN);
    }

	/**
	 * Class destructor
	 */
	public function __destruct() {
	    if (is_resource($this->_fp)) {
	      fclose($this->_fp);
	    }
	}

}