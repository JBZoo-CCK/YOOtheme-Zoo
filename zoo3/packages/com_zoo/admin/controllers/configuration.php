<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ConfigurationController
		The controller class for application configuration
*/
class ConfigurationController extends AppController {

	public $application;

	public function __construct($default = array()) {
		parent::__construct($default);

		// set table
		$this->table = $this->app->table->application;

		// get application
		$this->application 	= $this->app->zoo->getApplication();

		// check ACL
		if (!$this->application->isAdmin()) {
			throw new ConfigurationControllerException("Invalid Access Permissions!", 1);
		}

		// set base url
		$this->baseurl = $this->app->link(array('controller' => $this->controller), false);

		// register tasks
		$this->registerTask('applyassignelements', 'saveassignelements');
		$this->registerTask('apply', 'save');
	}

	public function display($cachable = false, $urlparams = false) {

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Config'));
		$this->app->toolbar->apply();
		$this->app->zoo->toolbarHelp();

		// get params
		$this->params = $this->application->getParams();

		// template select
		$options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Template').' -'));
		foreach ($this->application->getTemplates() as $template) {
			$options[] = $this->app->html->_('select.option', $template->name, $template->getMetaData('name'));
		}

		$this->lists['select_template'] = $this->app->html->_('select.genericlist',  $options, 'template', '', 'value', 'text', $this->params->get('template'));

		// get permission form
		$xml = simplexml_load_file(JPATH_COMPONENT . '/models/forms/permissions.xml');

		$this->permissions = JForm::getInstance('com_zoo.new', $xml->asXML());
		$this->permissions->bind(array('asset_id' => $this->application->asset_id));
		$this->assetPermissions = array();
		$asset = JTable::getInstance('Asset');

		foreach ($this->application->getTypes() as $typeName => $type) {
			$xml->fieldset->field->attributes()->section = 'type';
			$xml->fieldset->field->attributes()->name = 'rules_' . $typeName;
			$this->assetPermissions[$typeName] = JForm::getInstance('com_zoo.new.' . $typeName, $xml->asXML());

			if ($asset->loadByName($type->getAssetName())) {
				$assetName = $type->getAssetName();
			} else {
				$assetName = $this->application->asset_id;
			}
			$this->assetPermissions[$typeName]->bind(array('asset_id' => $assetName));
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

			// set params
			$params = $this->application
				->getParams()
				->remove('global.')
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
			$this->table->save($this->application);

			// set redirect message
			$msg = JText::_('Application Saved');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Saving Application').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function getApplicationParams() {

		// init vars
		$template     = $this->app->request->getCmd('template');

		// get params
		$this->params = $this->application->getParams();

		// set template
		$this->params->set('template', $template);

		// display view
		$this->getView()->setLayout('_applicationparams')->display();
	}

	public function importExport() {

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Import / Export'));
		$this->app->zoo->toolbarHelp();

		$this->exporter = $this->app->export->getExporters('Zoo v2');

		// display view
		$this->getView()->setLayout('importexport')->display();
	}

	public function importFrom() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		$exporter = $this->app->request->getString('exporter');

		try {

			$xml = $this->app->export->create($exporter)->export();

			$file = rtrim($this->app->system->config->get('tmp_path'), '\/') . '/' . $this->app->utility->generateUUID() . '.tmp';
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
			JFile::write($file, $xml);

		} catch (Exception $e) {

			// raise error on exception
			$this->app->error->raiseNotice(0, JText::_('Error During Export').' ('.$e.')');
			$this->setRedirect($this->baseurl.'&task=importexport');
			return;

		}

		$this->_import($file);

	}

	public function import() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		$userfile = null;

		$jsonfile = $this->app->request->getVar('import-json', array(), 'files', 'array');

		try {

			// validate
			$validator = $this->app->validator->create('file', array('extensions' => array('json')));
			$userfile = $validator->clean($jsonfile);
			$type = 'json';

		} catch (AppValidatorException $e) {}

		$csvfile = $this->app->request->getVar('import-csv', array(), 'files', 'array');

		try {

			// validate
			$validator = $this->app->validator->create('file', array('extensions' => array('csv')));
			$userfile = $validator->clean($csvfile);
			$type = 'csv';

		} catch (AppValidatorException $e) {}

		if (!empty($userfile)) {
			$file = rtrim($this->app->system->config->get('tmp_path'), '\/') . '/' . basename($userfile['tmp_name']);
			if (JFile::upload($userfile['tmp_name'], $file)) {

				$this->_import($file, $type);

			} else {
				// raise error on exception
				$this->app->error->raiseNotice(0, JText::_('Error Importing (Unable to upload file.)'));
				$this->setRedirect($this->baseurl.'&task=importexport');
				return;
			}
		} else {
			// raise error on exception
			$this->app->error->raiseNotice(0, JText::_('Error Importing (Unable to upload file.)'));
			$this->setRedirect($this->baseurl.'&task=importexport');
			return;
		}


	}

	public function importCSV() {

		$file = $this->app->request->getCmd('file', '');
		$file = rtrim($this->app->system->config->get('tmp_path'), '\/') . '/' . $file;

		$this->_import($file, 'importcsv');
	}

	protected function _import($file, $type = 'json') {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Import').': '.$this->application->name);
		$this->app->toolbar->cancel('importexport', 'Cancel');
		$this->app->zoo->toolbarHelp();

		// set_time_limit doesn't work in safe mode
        if (!ini_get('safe_mode')) {
		    @set_time_limit(0);
        }

		$layout = '';
		switch ($type) {
			case 'xml':
				$this->app->error->raiseWarning(0, 'XML import is not supported since ZOO 2.5!');
				$this->importExport();
				break;
			case 'json':
				if (JFile::exists($file) && $data = $this->app->data->create(file_get_contents($file))) {

					$this->info = $this->app->import->getImportInfo($data);
					$this->file = basename($file);

				} else {

					// raise error on exception
					$this->app->error->raiseNotice(0, JText::_('Error Importing (Not a valid JSON file)'));
					$this->setRedirect($this->baseurl.'&task=importexport');
					return;

				}
				$layout = 'importjson';
				break;
			case 'csv':

				$this->file = basename($file);

				$layout = 'configcsv';
				break;
			case 'importcsv':
				$this->contains_headers = $this->app->request->getBool('contains-headers', false);
				$this->field_separator	= $this->app->request->getString('field-separator', ',');
				$this->field_separator	= empty($this->field_separator) ? ',' : substr($this->field_separator, 0, 1);
				$this->field_enclosure	= $this->app->request->getString('field-enclosure', '"');
				$this->field_enclosure	= empty($this->field_enclosure) ? '"' : substr($this->field_enclosure, 0, 1);

				$this->info = $this->app->import->getImportInfoCSV($file, $this->contains_headers, $this->field_separator, $this->field_enclosure);
				$this->file = basename($file);

				$layout = 'importcsv';
				break;
		}

		// display view
		$this->getView()->setLayout($layout)->display();

	}

	public function doImport() {

		// init vars
		$import_frontpage   = $this->app->request->getBool('import-frontpage', false);
		$import_categories  = $this->app->request->getBool('import-categories', false);
		$element_assignment = $this->app->request->get('element-assign', 'array', array());
		$types				= $this->app->request->get('types', 'array', array());
		$file 				= $this->app->request->getCmd('file', '');
		$file 				= rtrim($this->app->system->config->get('tmp_path'), '\/') . '/' . $file;

		if (JFile::exists($file)) {
			$this->app->import->import($file, $import_frontpage, $import_categories, $element_assignment, $types);
		}

		$this->setRedirect($this->baseurl.'&task=importexport', JText::_('Import successfull'));
	}

	public function doImportCSV() {

		// init vars
		$contains_headers   = $this->app->request->getBool('contains-headers', false);
		$field_separator    = $this->app->request->getString('field-separator', ',');
		$field_enclosure    = $this->app->request->getString('field-enclosure', '"');
		$element_assignment = $this->app->request->get('element-assign', 'array', array());
		$type				= $this->app->request->getCmd('type', '');
		$file 				= $this->app->request->getCmd('file', '');
		$file 				= rtrim($this->app->system->config->get('tmp_path'), '\/') . '/' . $file;

		if (JFile::exists($file)) {
			$this->app->import->importCSV($file, $type, $contains_headers, $field_separator, $field_enclosure, $element_assignment);
		}

		$this->setRedirect($this->baseurl.'&task=importexport', JText::_('Import successfull'));
	}

	public function doExport() {

		$exporter = $this->app->request->getCmd('exporter');

		if ($exporter) {

			try {

				// set_time_limit doesn't work in safe mode
		        if (!ini_get('safe_mode')) {
				    @set_time_limit(0);
		        }

				$json = $this->app->export->create($exporter)->export();

				header("Pragma: public");
		        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		        header("Expires: 0");
		        header("Content-Transfer-Encoding: binary");
				header ("Content-Type: application/json");
				header('Content-Disposition: attachment;'
				.' filename="'.JFilterOutput::stringURLSafe($this->application->name).'.json";'
				);

				echo $json;

			} catch (AppExporterException $e) {

				// raise error on exception
				$this->app->error->raiseNotice(0, JText::_('Error Exporting').' ('.$e.')');
				$this->setRedirect($this->baseurl.'&task=importexport');
				return;

			}
		}
	}

	public function doExportCSV() {

		//init vars
		$files = array();

		try {

			foreach ($this->application->getTypes() as $type) {
				if ($file = $this->app->export->toCSV($type)) {
					$files[] = $file;
				}
			}

			if (empty($files)) {
				throw new AppException(JText::sprintf('There are no items to export'));
			}

			$filepath = $this->app->path->path("tmp:").'/'.$this->application->getGroup().'.zip';
			$zip = $this->app->archive->open($filepath, 'zip');
			$zip->create($files, PCLZIP_OPT_REMOVE_ALL_PATH);
			if (is_readable($filepath) && JFile::exists($filepath)) {
				$this->app->filesystem->output($filepath);
				$files[] = $filepath;
				foreach ($files as $file) {
					if (JFile::exists($file)) {
						JFile::delete($file);
					}
				}
			} else {
				throw new AppException(JText::sprintf('Unable to create file %s', $filepath));
			}

		} catch (AppException $e) {
				// raise error on exception
				$this->app->error->raiseNotice(0, JText::_('Error Exporting').' ('.$e.')');
				$this->setRedirect($this->baseurl.'&task=importexport');
				return;
		}

	}

}

/*
	Class: ConfigurationControllerException
*/
class ConfigurationControllerException extends AppException {}