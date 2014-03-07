<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper to help creating an AppMail object
 * 
 * @package Framework.Helpers
 */
class MailHelper extends AppHelper {

	/**
	 * Class constructor
	 * 
	 * @param App $app A reference to the global App object
	 */
	public function __construct($app) {
		parent::__construct($app);

		// load class
		$this->app->loader->register('AppMail', 'classes:mail.php');
	}

	/**
	 * Get a new AppMail object
	 * 
	 * @return AppMail The new AppMail object
	 * 
	 * @since 1.0.0
	 */
	public function create() {
		return new AppMail($this->app);
	}
	
}