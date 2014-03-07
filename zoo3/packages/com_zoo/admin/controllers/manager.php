<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ManagerController
		The controller class for application manager
*/
class ManagerController extends AppController {

	public $group;
	public $application;

	public function __construct($default = array()) {
		parent::__construct($default);

		// set base url
		$this->baseurl = $this->app->link(array('controller' => $this->controller), false);

		// get application group
		$this->group = $this->app->request->getString('group');

		// if group exists
		if ($this->group) {

			// add group to base url
			$this->baseurl .= '&group='.$this->group;

			// create application object
			$this->application = $this->app->object->create('Application');
			$this->application->setGroup($this->group);
		}

		// register tasks
		$this->registerTask('addtype', 'edittype');
		$this->registerTask('applytype', 'savetype');
		$this->registerTask('applyelements', 'saveelements');
		$this->registerTask('applyassignelements', 'saveassignelements');
		$this->registerTask('applysubmission', 'savesubmission');
	}

	public function display($cachable = false, $urlparams = false) {

		// set toolbar items
		$this->app->toolbar->title(JText::_('App Manager'), $this->app->get('icon'));
		JToolBar::getInstance('toolbar')->appendButton('Popup', 'stats', 'Check For Modifications', JRoute::_(JUri::root() . 'administrator/index.php?option='.$this->app->component->self->name.'&controller='.$this->controller.'&task=checkmodifications&tmpl=component', true, -1), 570, 350);
		JToolBar::getInstance('toolbar')->appendButton('Popup', 'preview', 'Check Requirements', JRoute::_(JUri::root() . 'administrator/index.php?option='.$this->app->component->self->name.'&controller='.$this->controller.'&task=checkrequirements&tmpl=component', true, -1), 570, 350);
		if ($this->app->get('cache_routes', false)) {
			$this->app->toolbar->custom('disableRouteCaching', 'options', 'Disable Route Caching', 'Disable Route Caching', false);
		} else {
			$this->app->toolbar->custom('enableRouteCaching', 'options', 'Enable Route Caching', 'Enable Route Caching', false);
		}
		$this->app->toolbar->custom('cleandb', 'refresh', 'Refresh', 'Clean Database', false);
		$this->app->toolbar->custom('dobackup', 'archive', 'Backup Database', 'Backup Database', false);
		$this->app->zoo->toolbarHelp();

		// get applications
		$this->applications = $this->app->application->groups();

		// display view
		$this->getView()->display();
	}

	public function info() {

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Information').': '.$this->application->getMetaData('name'));
		$this->app->toolbar->custom('doexport', 'archive', 'Archive', 'Export', false);
		$this->app->toolbar->custom('uninstallapplication', 'delete', 'Delete', 'Uninstall', false);
		$this->app->toolbar->deleteList('APP_DELETE_WARNING', 'removeapplication');
		$this->app->zoo->toolbarHelp();

		// get application instances for selected group
		$this->applications = $this->app->application->getApplications($this->application->getGroup());

		// display view
		$this->getView()->setLayout('info')->display();
	}

	public function installApplication() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// get the uploaded file information
		$userfile = $this->app->request->getVar('install_package', null, 'files', 'array');

		try {

			$result = $this->app->install->installApplicationFromUserfile($userfile);
			$update = $result == 2 ? 'updated' : 'installed';

			// set redirect message
			$msg = JText::sprintf('Application group (%s) successfully.', $update);

		} catch (InstallHelperException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::sprintf('Error installing Application group (%s).', $e));
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function uninstallApplication() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		try {

			$this->app->install->uninstallApplication($this->application);

			// set redirect message
			$msg = JText::_('Application group uninstalled successful.');
			$link = $this->baseurl;

			// remove current group from redirect link
			$link = str_replace('&group='.$this->group, '', $link);
		} catch (InstallHelperException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::sprintf('Error uninstalling application group (%s).', $e));
			$msg = null;
			$link = $this->baseurl.'&task=info';

		}

		$this->setRedirect($link, $msg);
	}

	public function removeApplication() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a application to delete'));
		}

		try {

			$table = $this->app->table->application;

			// delete applications
			foreach ($cid as $id) {
				$table->delete($table->get($id));
			}

			// set redirect message
			$msg = JText::_('Application Deleted');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::sprintf('Error Deleting Application (%s).', $e));
			$msg = null;

		}

		$this->setRedirect($this->baseurl.'&task=info', $msg);
	}

	public function types() {

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Types').': ' . $this->application->getMetaData('name'));
		$this->app->toolbar->addNew('addtype');
		$this->app->toolbar->custom('copytype', 'copy', '', 'Copy');
		$this->app->toolbar->deleteList('', 'removetype');
		$this->app->toolbar->editList('edittype');
		$this->app->zoo->toolbarHelp();

		// get types
		$this->types = $this->application->getTypes();

		// get templates
		$this->templates = $this->application->getTemplates();

		// get extensions / trigger layout init event
		$this->extensions = $this->app->event->dispatcher->notify($this->app->event->create($this->app, 'layout:init'))->getReturnValue();

		// display view
		$this->getView()->setLayout('types')->display();
	}

	public function editType() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// get request vars
		$cid  = $this->app->request->get('cid.0', 'string', '');
		$this->edit = $cid ? true : false;

		// get type
		if (empty($cid)) {
			$this->type = $this->app->object->create('Type', array(null, $this->application));
		} else {
			$this->type = $this->application->getType($cid);
		}

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Type').': '.$this->type->name.' <small><small>[ '.($this->edit ? JText::_('Edit') : JText::_('New')).' ]</small></small>');
		$this->app->toolbar->apply('applytype');
		$this->app->toolbar->save('savetype');
		$this->app->toolbar->cancel('types', $this->edit ?	'Close' : 'Cancel');
		$this->app->zoo->toolbarHelp();

		// display view
		ob_start();
		$this->getView()->setLayout('edittype')->display();
		$output = ob_get_contents();
		ob_end_clean();

		// trigger edit event
		$this->app->event->dispatcher->notify($this->app->event->create($this->type, 'type:editdisplay', array('html' => &$output)));

		echo $output;
	}

	public function copyType() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$msg = '';
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a type to copy'));
		}

		// copy types
		foreach ($cid as $id) {
			try {

				// get type
				$type = $this->application->getType($id);

				// copy type
				$copy			  = $this->app->object->create('Type', array(null, $this->application));
				$copy->identifier = $type->identifier.'-copy';                   // set copied alias
				$this->app->type->setUniqueIndentifier($copy);	// set unique identifier
				$copy->name       = sprintf('%s (%s)', $type->name, JText::_('Copy')); // set copied name

				// give elements a new unique id
				$elements = array();
				foreach ($type->elements as $identifier => $element) {
					if ($type->getElement($identifier) && $type->getElement($identifier)->getGroup() != 'Core') {
						$elements[$this->app->utility->generateUUID()] = $element;
					} else {
						$elements[$identifier] = $element;
					}
				}
				$copy->elements = $elements;

				// save copied type
				$copy->save();

				// trigger copied event
				$this->app->event->dispatcher->notify($this->app->event->create($copy, 'type:copied', array('old_id' => $id)));

				$msg = JText::_('Type Copied');

			} catch (AppException $e) {

				// raise notice on exception
				$this->app->error->raiseNotice(0, JText::sprintf('Error Copying Type (%s).', $e));
				$msg = null;
				break;

			}
		}

		$this->setRedirect($this->baseurl.'&task=types', $msg);
	}

	public function saveType() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$post = $this->app->request->get('post:', 'array', array());
		$cid  = $this->app->request->get('cid.0', 'string', '');

		// get type
		$type = $this->application->getType($cid);

		// type is new ?
		if (!$type) {
			$type = $this->app->object->create('Type', array(null, $this->application));
		}

		// filter identifier
		$post['identifier'] = $this->app->string->sluggify($post['identifier'] == '' ? $post['name'] : $post['identifier'], true);

		try {

			// set post data and save type
			$type->bind($post);

			// ensure unique identifier
 			$this->app->type->setUniqueIndentifier($type);

			// save type
            $type->save();

			// set redirect message
			$msg = JText::_('Type Saved');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::sprintf('Error Saving Type (%s).', $e));
			$this->_task = 'apply';
			$msg = null;

		}

		switch ($this->getTask()) {
			case 'applytype':
				$link = $this->baseurl.'&task=edittype&cid[]='.$type->id;
				break;
			case 'savetype':
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	public function removeType() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$msg = '';
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a type to delete'));
		}

		foreach ($cid as $id) {
			try {

				// delete type
				$type = $this->application->getType($id);
				$type->delete();

				// trigger after save event
				$this->app->event->dispatcher->notify($this->app->event->create($type, 'type:deleted'));

				// set redirect message
				$msg = JText::_('Type Deleted');

			} catch (AppException $e) {

				// raise notice on exception
				$this->app->error->raiseNotice(0, JText::sprintf('Error Deleting Type (%s).', $e));
				$msg = null;
				break;

			}
		}

		$this->setRedirect($this->baseurl.'&task=types', $msg);
	}

	public function editElements() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// get request vars
		$cid = $this->app->request->get('cid.0', 'string', '');

		// get type
		$this->type = $this->application->getType($cid);

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Type').': '.$this->type->name.' <small><small>[ '.JText::_('Edit elements').' ]</small></small>');
		$this->app->toolbar->apply('applyelements');
		$this->app->toolbar->save('saveelements');
		$this->app->toolbar->cancel('types', 'Close');
		$this->app->zoo->toolbarHelp();

		// sort elements by group
		$this->elements = array();
		foreach ($this->app->element->getAll() as $element) {
			$this->elements[$element->getGroup()][$element->getElementType()] = $element;
		}
		ksort($this->elements);
		foreach ($this->elements as $group => $elements) {
			ksort($elements);
			$this->elements[$group] = $elements;
		}

		// display view
		$this->getView()->setLayout('editElements')->display();
	}

	public function addElement() {

		// get request vars
		$element = $this->app->request->getWord('element', 'text');

		// load element
		$this->element = $this->app->element->create($element);
		$this->element->identifier = $this->app->utility->generateUUID();

		// display view
		$this->getView()->setLayout('addElement')->display();
	}

	public function saveElements() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$post = $this->app->request->get('post:', 'array', array());
		$cid  = $this->app->request->get('cid.0', 'string', '');

		try {

			// save types elements
			$type = $this->application->getType($cid);

			// bind and save elements
			$type->bindElements($post)->save();

			// reset related item search data
			$table = $this->app->table->item;
			$items = $table->getByType($type->id, $this->application->id);
			foreach ($items as $item) {
				$table->save($item);
			}

			$msg = JText::_('Elements Saved');

		} catch (AppException $e) {

			$this->app->error->raiseNotice(0, JText::sprintf('Error Saving Elements (%s)', $e));
			$this->_task = 'applyelements';
			$msg = null;

		}

		switch ($this->getTask()) {
			case 'applyelements':
				$link = $this->baseurl.'&task=editelements&cid[]='.$type->id;
				break;
			case 'saveelements':
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	public function assignElements() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// init vars
		$type				 = $this->app->request->getString('type');
		$this->relative_path = urldecode($this->app->request->getVar('path'));
		$this->path			 = $this->relative_path ? JPATH_ROOT . '/' . $this->relative_path : '';
		$this->layout		 = $this->app->request->getString('layout');

		$dispatcher = JDispatcher::getInstance();
		if (strpos($this->relative_path, 'plugins') === 0) {
			@list($_, $plugin_type, $plugin_name) = explode('/', $this->relative_path);
			JPluginHelper::importPlugin($plugin_type, $plugin_name);
		}
		$dispatcher->trigger('registerZOOEvents');

		// get type
		$this->type = $this->application->getType($type);

        if ($this->type) {
            // set toolbar items
            $this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Type').': '.$this->type->name.' <small><small>[ '.JText::_('Assign elements').': '. $this->layout .' ]</small></small>');
            $this->app->toolbar->apply('applyassignelements');
			$this->app->toolbar->save('saveassignelements');
            $this->app->toolbar->cancel('types');
            $this->app->zoo->toolbarHelp();

            // get renderer
            $renderer = $this->app->renderer->create('item')->addPath($this->path);

            // get positions and config
            $this->config = $renderer->getConfig('item')->get($this->group.'.'.$type.'.'.$this->layout);

            $prefix = 'item.';
            if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$type)) {
                $prefix .= $type.'.';
            }
            $this->positions = $renderer->getPositions($prefix.$this->layout);

			// display view
			ob_start();
			$this->getView()->setLayout('assignelements')->display();
			$output = ob_get_contents();
			ob_end_clean();

			// trigger edit event
			$this->app->event->dispatcher->notify($this->app->event->create($this->type, 'type:assignelements', array('html' => &$output)));

			echo $output;


        } else {

			$this->app->error->raiseNotice(0, JText::sprintf('Unable to find type (%s).', $type));
			$this->setRedirect($this->baseurl . '&task=types&group=' . $this->application->getGroup());

		}
	}

	public function saveAssignElements() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$type		   = $this->app->request->getString('type');
		$layout		   = $this->app->request->getString('layout');
		$relative_path = $this->app->request->getVar('path');
		$path		   = $relative_path ? JPATH_ROOT . '/' . urldecode($relative_path) : '';
		$positions	   = $this->app->request->getVar('positions', array(), 'post', 'array');

		// unset unassigned position
		unset($positions['unassigned']);

		// get renderer
		$renderer = $this->app->renderer->create('item')->addPath($path);

		// clean config
		$config = $renderer->getConfig('item');
		foreach ($config as $key => $value) {
			$parts = explode('.', $key);
			if ($parts[0] == $this->group && !$this->application->getType($parts[1])) {
				$config->remove($key);
			}
		}

		// save config
		$config->set($this->group.'.'.$type.'.'.$layout, $positions);
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');

		switch ($this->getTask()) {
			case 'applyassignelements':
				$link  = $this->baseurl.'&task=assignelements&type='.$type.'&layout='.$layout.'&path='.$relative_path;
				break;
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, JText::_('Elements Assigned'));
	}

	public function assignSubmission() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// init vars
		$type           = $this->app->request->getString('type');
		$this->template = $this->app->request->getString('template');
		$this->layout   = $this->app->request->getString('layout');

		// get type
		$this->type = $this->application->getType($type);

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Type').': '.$this->type->name.' <small><small>[ '.JText::_('Assign Submittable elements').': '. $this->layout .' ]</small></small>');
		$this->app->toolbar->apply('applysubmission');
		$this->app->toolbar->save('savesubmission');
		$this->app->toolbar->cancel('types');
		$this->app->zoo->toolbarHelp();

		// for template
		$this->path = $this->application->getPath().'/templates/'.$this->template;

		// get renderer
		$renderer = $this->app->renderer->create('submission')->addPath($this->path);

		// get positions and config
		$this->config = $renderer->getConfig('item')->get($this->group.'.'.$type.'.'.$this->layout);

		$prefix = 'item.';
		if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$type)) {
			$prefix .= $type.'.';
		}
		$this->positions = $renderer->getPositions($prefix.$this->layout);

		// display view
		$this->getView()->setLayout('assignsubmission')->display();
	}

	public function saveSubmission() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$type      = $this->app->request->getString('type');
		$template  = $this->app->request->getString('template');
		$layout    = $this->app->request->getString('layout');
		$positions = $this->app->request->getVar('positions', array(), 'post', 'array');

		// unset unassigned position
		unset($positions['unassigned']);

		// for template, module
		$path = '';
		if ($template) {
			$path = $this->application->getPath().'/templates/'.$template;
		}

		// get renderer
		$renderer = $this->app->renderer->create('submission')->addPath($path);

		// clean config
		$config = $renderer->getConfig('item');
		foreach ($config as $key => $value) {
			$parts = explode('.', $key);
			if ($parts[0] == $this->group && !$this->application->getType($parts[1])) {
				$config->remove($key);
			}
		}

		// save config
		$config->set($this->group.'.'.$type.'.'.$layout, $positions);
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');

		switch ($this->getTask()) {
			case 'applysubmission':
				$link  = $this->baseurl.'&task=assignsubmission&type='.$type.'&layout='.$layout;
				$link .= $template ? '&template='.$template : null;
				break;
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, JText::_('Submitable Elements Assigned'));
	}

	public function doExport() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		$filepath = $this->app->path->path("tmp:").'/'.$this->application->getGroup().'.zip';
		$read_directory = $this->application->getPath() . '/';
		$zip = $this->app->archive->open($filepath, 'zip');
		$files = $this->app->path->files($this->application->getResource(), true);
		$files = array_map(create_function('$file', 'return "'.$read_directory.'".$file;'), $files);
		$zip->create($files, PCLZIP_OPT_REMOVE_PATH, $read_directory);
		if (is_readable($filepath) && JFile::exists($filepath)) {
			$this->app->filesystem->output($filepath);
			if (!JFile::delete($filepath)) {
				$this->app->error->raiseNotice(0, JText::sprintf('Unable to delete file(%s).', $filepath));
				$this->setRedirect($this->baseurl.'&task=info');
			}
		} else {
			$this->app->error->raiseNotice(0, JText::sprintf('Unable to create file (%s).', $filepath));
			$this->setRedirect($this->baseurl.'&task=info');
		}

	}

    public function checkRequirements() {

		$this->app->loader->register('AppRequirements', 'installation:requirements.php');

        $requirements = $this->app->object->create('AppRequirements');
        $requirements->checkRequirements();
        $requirements->displayResults();

    }

    public function checkModifications() {

		// add system.css for
		$this->app->document->addStylesheet("root:administrator/templates/system/css/system.css");

		try {

			$this->results = $this->app->modification->check();

			// display view
			$this->getView()->setLayout('modifications')->display();

		} catch (AppModificationException $e) {

			$this->app->error->raiseNotice(0, $e);

		}

    }

    public function cleanModifications() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		try {

			$this->app->modification->clean();
			$msg = JText::_('Unknown files removed.');

		} catch (AppModificationException $e) {

			$msg = JText::_('Error cleaning ZOO.');
			$this->app->error->raiseNotice(0, $e);

		}

		$route = JRoute::_($this->baseurl.'&task=checkmodifications&tmpl=component', false);
		$this->setRedirect($route, $msg);

    }

    public function verify() {

		$result = false;

		try {

			$result = $this->app->modification->verify();

		} catch (AppModificationException $e) {}

		echo json_encode(compact('result'));

    }

	public function doBackup() {

		if ($result = $this->app->backup->all()) {

			$result = $this->app->backup->generateHeader() . $result;
			$size = strlen($result);

			while (@ob_end_clean());

			header("Pragma: public");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Expires: 0");
			header("Content-Transfer-Encoding: binary");
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment;'
				.' filename="zoo-db-backup-'.time().'-'.(md5(implode(',', $this->app->backup->getTables()))).'.sql";'
				.' modification-date="'.date('r').'";'
				.' size='.$size.';');
			header("Content-Length: ".$size);

			echo $result;
			return;
		}

		// raise error on exception
		$this->app->error->raiseNotice(0, JText::_('Error Creating Backup'));
		$this->setRedirect($this->baseurl);

	}

	public function restoreBackup() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// get the uploaded file information
		$userfile = $this->app->request->getVar('backupfile', null, 'files', 'array');

		try {

			$file = $this->app->validator->create('file', array('extension' => array('sql')))->clean($userfile);

			$this->app->backup->restore($file['tmp_name']);
			$msg = JText::_('Database backup successfully restored');

		} catch (AppValidatorException $e) {

			$msg = '';
			$this->app->error->raiseNotice(0, "Error uploading backup file. ($e) Please upload .sql files only.");

		} catch (RuntimeException $e) {

			$msg = '';
			$this->app->error->raiseNotice(0, JText::sprintf("Error restoring ZOO backup. (%s)", $e->getMessage()));

		}

		$this->setRedirect($this->baseurl, $msg);

	}

	public function cleanDB() {

		// set toolbar items
		$this->app->toolbar->title('Cleaning database, please don\'t leave this page');

		$this->item_count = $this->app->table->item->count();
		$this->steps = (int) (11 + ($this->item_count / 10));

		// display view
		$this->getView()->setLayout('cleandb')->display();

	}

	public function cleanDBStep() {

		$step = $this->app->request->getInt('step', 0);
		$row = $this->app->request->getInt('row', 0);
		$offset = $this->app->request->getInt('offset', 0);

		// init vars
		$items_per_run = 10;
		$db = $this->app->database;
		$msg = '';
		$error = '';

		switch ($step) {
			case '1':
				if ($apps = $this->app->path->dirs('applications:')) {
					$db->query(sprintf("DELETE FROM ".ZOO_TABLE_APPLICATION." WHERE application_group NOT IN ('%s')", implode("', '", $apps)));
				}
				$msg = 'Cleaning application folders...';
				break;
			case '2':
				$db->query("DELETE FROM ".ZOO_TABLE_ITEM." WHERE type = ''");
				$row += $db->getAffectedRows();
				$msg = 'Cleaning items of undefined type...';
				break;
			case '3':
				$db->query("DELETE FROM ".ZOO_TABLE_ITEM." WHERE NOT EXISTS (SELECT id FROM ".ZOO_TABLE_APPLICATION." WHERE id = application_id)");
				$row += $db->getAffectedRows();
				$msg = 'Cleaning items that don\'t belong to an application...';
				break;
			case '4':
				$db->query("DELETE FROM ".ZOO_TABLE_CATEGORY." WHERE NOT EXISTS (SELECT id FROM ".ZOO_TABLE_APPLICATION." WHERE id = application_id)");
				$row += $db->getAffectedRows();
				$msg = 'Cleaning categories that don\'t belong to an application...';
				break;
			case '5':
				$db->query("DELETE FROM ".ZOO_TABLE_SUBMISSION." WHERE NOT EXISTS (SELECT id FROM ".ZOO_TABLE_APPLICATION." WHERE id = application_id)");
				$row += $db->getAffectedRows();
				$msg = 'Cleaning submissions that don\'t belong to an application...';
				break;
			case '6':
				$db->query("DELETE FROM ".ZOO_TABLE_TAG." WHERE NOT EXISTS (SELECT id FROM ".ZOO_TABLE_ITEM." WHERE id = item_id)");
				$row += $db->getAffectedRows();
				$msg = 'Cleaning tags that don\'t belong to an item...';
				break;
			case '7':
				$db->query("DELETE FROM ".ZOO_TABLE_COMMENT." WHERE NOT EXISTS (SELECT id FROM ".ZOO_TABLE_ITEM." WHERE id = item_id)");
				$row += $db->getAffectedRows();
				$msg = 'Cleaning comments that don\'t belong to an item...';
				break;
			case '8':
				$db->query("DELETE FROM ".ZOO_TABLE_RATING." WHERE NOT EXISTS (SELECT id FROM ".ZOO_TABLE_ITEM." WHERE id = item_id)");
				$row += $db->getAffectedRows();
				$msg = 'Cleaning ratings that don\'t belong to an item...';
				break;
			case '9':
				$db->query("DELETE FROM ".ZOO_TABLE_SEARCH." WHERE NOT EXISTS (SELECT id FROM ".ZOO_TABLE_ITEM." WHERE id = item_id)");
				$row += $db->getAffectedRows();
				$msg = 'Cleaning search values that don\'t belong to an item...';
				break;
			case '10':
				$db->query("DELETE FROM ".ZOO_TABLE_CATEGORY_ITEM." WHERE category_id != 0 AND (NOT EXISTS (SELECT id FROM ".ZOO_TABLE_ITEM." WHERE id = item_id) OR NOT EXISTS (SELECT id FROM ".ZOO_TABLE_CATEGORY." WHERE id = category_id))");
				$row += $db->getAffectedRows();
				$msg = 'Cleaning category to item relations...';
				break;
			case '11':
				// sanatize parent references
				$db->query("UPDATE ".ZOO_TABLE_CATEGORY." SET parent = 0 WHERE parent != 0 AND NOT EXISTS (SELECT id FROM (SELECT id FROM ".ZOO_TABLE_CATEGORY.") as t WHERE t.id = parent)");
				$db->query("UPDATE ".ZOO_TABLE_COMMENT." SET parent_id = 0 WHERE parent_id != 0 AND NOT EXISTS (SELECT id FROM (SELECT id FROM ".ZOO_TABLE_CATEGORY.") as t WHERE t.id = parent_id)");
				$msg = 'Cleaning category and comment parent relations...';
				break;
			case '12':

				// get the item table
				$table = $this->app->table->item;

				try {

					$items = $table->all(array('offset' => $offset, 'limit' => $items_per_run));
					if (empty($items)) {
						echo json_encode(array('forward' => $this->baseurl, 'message' => JText::sprintf('Cleaned database (Removed %s entries) and items search data has been updated.', $row)));
						return;
					}
					foreach ($items as $item) {
						try {

							$table->save($item);

						} catch (Exception $e) {
							$error = JText::sprintf("Error updating search data for item with id %s. (%s)", $item->id, $e);
						}
					}

				} catch (Exception $e) {

					$msg = '';
					$error = JText::sprintf("Error cleaning database. (%s)", $e);

				}
				$msg = sprintf('Resaving items %s to %s', $offset, $offset + $items_per_run);
				$step--;
				$offset += $items_per_run;
		}

		echo json_encode(array(
			'error' => $error,
			'message' => $msg,
			'step' => $step + ($offset / $items_per_run),
			'redirect' => $this->baseurl.sprintf('&task=cleandbstep&format=raw&step=%s&row=%s&offset=%s', ++$step, $row, $offset)
		));
	}

	public function hideUpdateNotification() {
		$this->app->update->hideUpdateNotification();
	}

	public function disableRouteCaching() {
		$this->_toggleRouteCaching(false);
	}

	public function enableRouteCaching() {
		$this->_toggleRouteCaching(true);
	}

	public function getAlias() {
		$name 		= $this->app->request->getString('name', '');
		$force_safe = $this->app->request->getBool('force_safe', false);
		echo json_encode($this->app->string->sluggify($name, $force_safe));
	}

	protected function _toggleRouteCaching($state) {
		$this->app->set('cache_routes', $state);
		$this->app->component->self->save();
		$this->app->route->clearCache();
		$this->setRedirect($this->baseurl);
	}

}

/*
	Class: ManagerControllerException
*/
class ManagerControllerException extends AppException {}