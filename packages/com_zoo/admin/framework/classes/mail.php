<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Mailer class. Utility class to send emails
 * 
 * @package Framework.Classes
 */
class AppMail {

	/**
	 * Reference to the global App object
	 * 
	 * @var App
	 * @since 1.0.0
	 */
	public $app;

	/**
	 * The mailer object
	 * 
	 * @var object
	 * @since 1.0.0
	 */
	protected $_mail;

	/**
	 * Class Constructor
	 * 
	 * @param App $app The global App object
	 */
	public function __construct($app) {

		// init vars
		$this->app   = $app;
		$this->_mail = $app->system->mailer;

	}

	/**
	 * Sets the body of the email
	 *
	 * When setting the body, checks if the string passed is an html page
	 * and set isHTML to true if so.
	 * 
	 * @param string $content The content of the body
	 * 
	 * @since 1.0.0
	 */
	public function setBody($content) {

		// auto-detect html
		if (stripos($content, '<html') !== false) {
			$this->_mail->IsHTML(true);
		}

		// set body
		$this->_mail->setBody($content);
	}

	/**
	 * Set the mail body using a template file
	 * 
	 * @param string $template The path to a template file. Can be use the registered paths
	 * @param array $args The list of arguments to pass on to the template
	 * 
	 * @since 1.0.0
	 */
	public function setBodyFromTemplate($template, $args = array()) {

		// init vars
		$__tmpl = $this->app->path->path($template);

		// does the template file exists ?
		if ($__tmpl == false) {
			throw new AppMailException("Mail Template $template not found");
		}

		// render the mail template
		extract($args);
		ob_start();
		include($__tmpl);
		$output = ob_get_contents();
		ob_end_clean();

		// set body
		$this->setBody($output);
	}
	
	/**
	 * Magic method to map all the methods of the mailer object
	 * 
	 * @see http://api.joomla.org/Joomla-Platform/Mail/JMail.html
	 * 
	 * @param string $method The method name
	 * @param array $args The arguments of the method
	 * 
	 * @return mixed The result of the method call
	 * 
	 * @since 1.0.0
	 */
    public function __call($method, $args) {
        return call_user_func_array(array($this->_mail, $method), $args);
    }

}

/**
 * Exception dedicated to the AppMail class
 * 
 * @see AppMail
 */
class AppMailException extends AppException {}