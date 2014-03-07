<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Email logger
 * 
 * @package Framework.Loggers
 * 
 * @see AppLogger
 */
class EmailLogger extends AppLogger {

	/**
	 * Format of the email
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $_format;
	
	/**
	 * Timestamp format
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $_time;
	
	/**
	 * Email address
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $_email;
	
	/**
	 * Email log
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $_log;

	/**
	 * Class constructor
	 * 
	 * @param string $email The email address
	 * @param string $format The format of the email with placeholders
	 * @param string $time The timeformat to user
	 */
	public function __construct($email, $format = '%time% %level% %type% %message%%EOL%', $time = '%b %d %H:%M:%S') {
		parent::__construct();

		// init vars
		$this->_format = $format;
		$this->_time = $time;
		$this->_email = $email;
		$this->_log = '';

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
	
		$this->_log .= strtr($this->_format, array(
	      '%time%'    => strftime($this->_time),
	      '%level%'   => str_pad('['.$this->getLevelText($level).']', 10),
	      '%type%'    => '{'.$type.'}',
	      '%message%' => $message,
	      '%EOL%'     => PHP_EOL,
	    ));

    }

	/**
	 * Class destructor
	 */
	public function __destruct() {
	
		// send mail, if log exists
		if ($this->_log) {
			$mail = $this->app->system->mailer;
			$mail->setSubject('Log Message');
			$mail->setBody($this->_log);
			$mail->addRecipient($this->_email);
			$mail->Send();
		}
		
	}

}