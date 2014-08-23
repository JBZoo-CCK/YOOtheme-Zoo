<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: NewController
		The controller class for creating a new application
*/
class NewController extends AppController {

	public $group;
	public $application;

	public function __construct($default = array()) {
		parent::__construct($default);

		// get application group
		$this->group = $this->app->request->getString('group');

		// set base url
		$this->baseurl = $this->app->link(array('controller' => $this->controller), false);

		// if group exists
		if ($this->group) {

			// add group to base url
			$this->baseurl .= '&group='.$this->group;

			// create application object
			$this->application = $this->app->object->create('Application');
			$this->application->setGroup($this->group);
		}
	}

	public function display($cachable = false, $urlparams = false) {

		// set toolbar items
		$this->app->toolbar->title(JText::_('New App'), $this->app->get('icon'));
		$this->app->zoo->toolbarHelp();

		// get applications
		$this->applications = $this->app->application->groups();

		// display view
		$this->getView()->display();
	}

	public function add() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('New App').': '.$this->application->getMetaData('name'));
		$this->app->toolbar->save();
		$this->app->toolbar->cancel('cancel', 'Cancel');
		$this->app->zoo->toolbarHelp();

		// get params
		$this->params = $this->application->getParams();

		// set default template
		$this->params->set('template', 'default');

		// template select
		$options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Template').' -'));
		foreach ($this->application->getTemplates() as $template) {
			$options[] = $this->app->html->_('select.option', $template->name, $template->getMetaData('name'));
		}

		$this->lists['select_template'] = $this->app->html->_('select.genericlist',  $options, 'template', '', 'value', 'text', $this->params->get('template'));

		// get permission form
		$xml = simplexml_load_file(JPATH_COMPONENT . '/models/forms/permissions.xml');

		$this->permissions = JForm::getInstance('com_zoo.new', $xml->asXML());
		$this->permissions->bind(array('asset_id' => 'com_zoo'));
		$this->assetPermissions = array();

		foreach ($this->application->getTypes() as $typeName => $type) {
			$xml->fieldset->field->attributes()->section = 'type';
			$xml->fieldset->field->attributes()->name = 'rules_' . $typeName;
			$this->assetPermissions[$typeName] = JForm::getInstance('com_zoo.new.' . $typeName, $xml->asXML());
			$this->assetPermissions[$typeName]->bind(array('asset_id' => 'com_zoo'));
		}

		// manipulate js in J25
		if ($this->app->joomla->isVersion('2.5')) {
			JDispatcher::getInstance()->attach(array('event' => 'onAfterDispatch', 'handler' => array($this, 'eventCallback')));
		}

		// display view
		$this->getView()->setLayout('application')->display();
	}

	public function eventCallback() {

		$script = $this->app->system->document->_script['text/javascript'];
		$types  = array_keys($this->application->getTypes());
		$types[]= 'application';

		$i = 3;
		$script = preg_replace_callback('/div#permissions-sliders\.pane-sliders/', function ($match) use (&$i, $types) {
			return 'div .zoo-'.$types[(int) ($i++ / 3) - 1].'-permissions';
		}, $script);

		$this->app->system->document->_script['text/javascript'] = $script;
	}

	public function save() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$post = $this->app->request->get('post:', 'array');

		try {

			// bind post
			self::bind($this->application, $post, array('params'));

			// Force alias
			if (!strlen(trim($this->application->alias))) {
				$this->application->alias = $this->app->string->sluggify($this->application->name);
			}
			$this->application->alias = $this->app->alias->application->getUniqueAlias($this->application->id, $this->app->string->sluggify($this->application->alias));

			// set params
			$params = $this->application
				->getParams()
				->remove('global.')
				->set('group', @$post['group'])
				->set('template', @$post['template'])
				->set('global.config.', @$post['params']['config'])
				->set('global.template.', @$post['params']['template']);

			if (isset($post['addons']) && is_array($post['addons'])) {
				foreach ($post['addons'] as $addon => $value) {
					$params->set("global.$addon.", $value);
				}
			}

			// add ACL rules to aplication object
			$this->application->rules = $post['rules'];

			foreach ($post as $key => $value) {
				if (stripos($key, 'rules_') === 0) {
					$this->application->assetRules[substr($key, 6)] = $value;
				}
			}

			// save application
			$this->app->table->application->save($this->application);

			// set redirect
			$msg  = JText::_('Application Saved');
			$link = $this->app->link(array('changeapp' => $this->application->id), false);

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Saving Application').' ('.$e.')');

			// set redirect
			$msg  = null;
			$link = $this->baseurl.'&task=add';

		}

		$this->setRedirect($link, $msg);
	}

	public function getApplicationParams() {

		// init vars
		$template     = $this->app->request->getCmd('template');
		$this->params = $this->application->getParams();

		// set template
		$this->params->set('template', $template);

		// display view
		$this->getView()->setLayout('_applicationparams')->display();
	}

}

/*
	Class: NewControllerException
*/
class NewControllerException extends AppException {}