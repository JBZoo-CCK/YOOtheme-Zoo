<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: UpdateController
		The controller class for updates
*/
class UpdateController extends AppController {

	public function __construct($default = array()) {
		parent::__construct($default);

		// set base url
		$this->baseurl = $this->app->link(array('controller' => $this->controller), false);

	}

	public function display($cachable = false, $urlparams = false) {

		// set toolbar items
		$this->app->toolbar->title(JText::_('ZOO Update'), $this->app->get('icon'));
		$this->app->zoo->toolbarHelp();

		$this->app->html->_('behavior.tooltip');

		if (!$this->update = $this->app->update->required()) {
			$this->app->system->application->redirect($this->app->link());
		}

		$this->notifications = $this->app->update->getNotifications();

		// display view
		$this->getView()->display();
	}

	public function step() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		$response = $this->app->update->run();

		echo json_encode($response);
	}

}

/*
	Class: UpdateAppControllerException
*/
class UpdateAppControllerException extends AppException {}