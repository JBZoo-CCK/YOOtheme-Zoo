<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: FrontpageController
		The controller class for frontpage
*/
class FrontpageController extends AppController {

	public $application;

	public function __construct($default = array()) {
		parent::__construct($default);

		// set table
		$this->table = $this->app->table->application;

		// get application
		$this->application 	= $this->app->zoo->getApplication();

		// check ACL
		if (!$this->application->canManageFrontpage()) {
			throw new FrontpageControllerException("Invalid Access Permissions", 1);
		}

		// set base url
		$this->baseurl = $this->app->link(array('controller' => $this->controller), false);

		// register tasks
		$this->registerTask('apply', 'save');

	}

	public function display($cachable = false, $urlparams = false) {

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Frontpage'));
		$this->app->toolbar->apply();
		$this->app->zoo->toolbarHelp();

		// get params
		$this->params = $this->application->getParams();

		// display view
		$this->getView()->display();
	}

	public function save() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$post = $this->app->request->get('post:', 'array');
		$post['description'] = $this->app->request->getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);

		try {

			// bind post
			self::bind($this->application, $post, array('params'));

			// set params
			$this->application->params = $this->application
				->getParams()
				->remove('content.')
				->remove('config.')
				->remove('template.')
				->set('content.', @$post['params']['content'])
				->set('config.', @$post['params']['config'])
				->set('template.', @$post['params']['template']);

			// save application
			$this->table->save($this->application);

			// set redirect message
			$msg = JText::_('Frontpage Saved');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Saving Frontpage').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

}

/*
	Class: FrontpageControllerException
*/
class FrontpageControllerException extends AppException {}