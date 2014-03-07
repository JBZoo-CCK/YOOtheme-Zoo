<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Object that represents an Application Instance
 *
 * @package Component.Classes
 */
class Application {

    /**
     * Primary key of the application record
     *
     * @var int
     * @since 2.0
     */
	public $id;

    /**
     * The name of the application
     *
     * @var string
     * @since 2.0
     */
	public $name;

    /**
     * The application alias
     *
     * @var string
     * @since 2.0
     */
	public $alias;

    /**
     * The description given to the application
     *
     * @var string
     * @since 2.0
     */
	public $description;

    /**
     * The application group (type)
     *
     * @var string
     * @since 2.0
     */
	public $application_group;

    /**
     * The parameters for this application
     *
     * @var JSONData
     * @since 2.0
     */
	public $params;

    /**
     * The name of the metadata file
     *
     * @var string
     * @since 2.0
     */
	public $metaxml_file = 'application.xml';

    /**
     * A reference to the global App object
     *
     * @var App
     * @since 2.0
     */
	public $app;

   	/**
   	 * The categories of the application
   	 *
   	 * @var array
   	 * @since 2.0
   	 */
	public $_categories;

    /**
     * The category tree
     *
     * @var array
     * @since 2.0
     */
	public $_category_tree;

	/**
	 * The metadata of the application
	 *
	 * @var SimpleXMLElement
	 * @since 2.0
	 */
	public $_metaxml;

	/**
	 * List of submission instances
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_submissions = array();

	/**
	 * List of types objects for this application
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_types = array();

	/**
	 * List of instances of the templates for this application
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_templates = array();

	/**
	 * Flag to check if the item count of the categories has been retrieved
	 *
	 * @var boolean
	 * @since 2.0
	 */
	protected $_itemcount = false;

 	/**
 	 * Class Constructor
 	 */
	public function __construct() {

		// init vars
		$app = App::getInstance('zoo');

		// decorate data as object
		$this->params = $app->parameter->create($this->params);
	}

	/**
	 * Dispatch the application using the default controller
	 *
	 * @since 2.0
	 */
	public function dispatch() {
		// delegate dispatch
		$this->app->dispatch('default');
	}

	/**
	 * Retrieve the application path
	 *
	 * @return String The application path
	 *
	 * @since 2.0
	 */
	public function getPath() {
		return $this->app->path->path($this->getResource());
	}

	/**
	 * Get the application resource path
	 *
	 * @return string The resource path for this application
	 *
	 * @since 2.0
	 */
	public function getResource() {
		return "applications:{$this->getGroup()}/";
	}

	/**
	 * Get the URI to the current application resource folder
	 *
	 * @return string The URI to the application resource folder
	 *
	 * @since 2.0
	 */
	public function getURI() {
		return $this->app->path->url($this->getResource());
	}

	/**
	 * Check if the application icon exists
	 *
	 * @return boolean True if the icon exists
	 *
	 * @since 2.0
	 */
	public function hasIcon() {
		return (bool) $this->app->path->path($this->getResource() . 'application.png');
	}

	/**
	 * Get the url to the icon of the application
	 *
	 * @return string The url to the icon
	 *
	 * @since 2.0
	 */
	public function getIcon() {
		return $this->app->path->url($this->hasIcon() ? $this->getResource() . 'application.png' : 'assets:images/zoo.png');
	}

	/**
	 * Get the url to the application info image
	 *
	 * @return string The url to the application info image
	 *
	 * @since 2.0
	 */
	public function getInfoImage() {
		if ($this->app->path->path($this->getResource() . 'application_info.png')) {
			return $this->app->path->url($this->getResource() . 'application_info.png');
		}
		return '';
	}

	/**
	 * Get the HTML code for the toolbar title
	 *
	 * @param string $title The title of the toolbar
	 *
	 * @return string The html code for the title
	 *
	 * @since 2.0
	 */
	public function getToolbarTitle($title) {

        if ($this->app->joomla->version->isCompatible('3.2')) {
            $html   = array('<h1 class="page-title">');
            $html[] = $this->hasIcon() ? '<img src="'.$this->getIcon().'" width="48" height="48" />' : null;
            $html[] = $title;
            $html[] = '</h1>';
        } else {
            $html   = array('<div class="header icon-48-'.(($this->hasIcon()) ? 'application"' : 'zoo"').'>');
            $html[] = $this->hasIcon() ? '<img src="'.$this->getIcon().'" width="48" height="48" style="margin-left:-55px;vertical-align:middle;" />' : null;
            $html[] = $title;
            $html[] = '</div>';
        }

		return implode("\n", $html);
	}

	/**
	 * Get the application group
	 *
	 * @return string The application group
	 *
	 * @since 2.0
	 */
	public function getGroup() {
		return $this->application_group;
	}

	/**
	 * Set the application group
	 *
	 * @param string $group The application group
	 *
	 * @return Application $this for chaining support
	 *
	 * @since 2.0
	 */
	public function setGroup($group) {
		$this->application_group = $group;
		return $this;
	}

	/**
	 * Get the list of categories for this application
	 *
	 * @param boolean $published Get only the published categories (default: false)
	 * @param boolean $item_count If the query should also fetch the list of item ids contained in the categories
	 * @param JUser $user JUser object to check for the categories access level
	 *
	 * @return array The list of categories
	 *
	 * @since 2.0
	 */
	public function getCategories($published = false, $item_count = false, $user = null) {

		// get categories
		if (empty($this->_categories) || (!$this->_itemcount && $item_count)) {
			$this->_categories = $this->app->table->category->getAll($this->id, $published, $item_count, $user);
		}

		$this->_itemcount = $item_count || $this->_itemcount;

		return $this->_categories;
	}

	/**
	 * Get the categories as a tree (nested array)
	 *
	 * @param Boolean $published If we should fetch only the published categories
	 * @param JUser $user The user to check the access rights
	 * @param boolean $item_count If we shouuld get the item count also
	 *
	 * @return array The categories tree
	 *
	 * @since 2.0
	 */
	public function getCategoryTree($published = false, $user = null, $item_count = false) {

		// get category tree
		if (empty($this->_category_tree) || (!$this->_itemcount && $item_count)) {
			// get categories and item count
			$categories = $this->getCategories($published, $item_count, $user);

			$this->_category_tree = $this->app->tree->build($categories, 'Category');
			$this->_category_tree[0]->application_id = $this->id;
		}

		$this->_itemcount = $item_count || $this->_itemcount;

		return $this->_category_tree;
	}

	/**
	 * Get the number of categories for this application
	 *
	 * @return int The number of categories
	 *
	 * @since 2.0
	 */
	public function getCategoryCount() {
		return $this->app->table->category->count(array('select' => 'id', 'conditions' => array('application_id=?',$this->id)));
	}

	/**
	 * Get the total item count for this application
	 *
	 * @return int The total number of items
	 *
	 * @since 2.0
	 */
	public function getItemCount() {
		return $this->app->table->item->getApplicationItemCount($this->id);
	}

	/**
	 * Get the template object
	 *
	 * @return Template The template object
	 *
	 * @since 2.0
	 */
	public function getTemplate() {
		$templates = $this->getTemplates();
		if (($name = $this->getParams()->get('template')) && isset($templates[$name])) {
			return $templates[$name];
		}

		return null;
	}

	/**
	 * Get the available templates for this application
	 *
	 * @return array The list of templates
	 *
	 * @since 2.0
	 */
	public function getTemplates() {

		if (empty($this->_templates)) {
			if ($folders = $this->app->path->dirs($this->getResource().'templates')) {
				foreach ($folders as $folder) {
					$this->_templates[$folder] = $this->app->template->create(array($folder, $this->getResource().'templates/'.$folder));
				}
			}
		}

		return $this->_templates;
	}

	/**
	 * Get a Type given its id
	 *
	 * @param string $id The type identifier
	 *
	 * @return Type The Type object
	 */
	public function getType($id) {
		$types = $this->getTypes();

		if (isset($types[$id])) {
			return $types[$id];
		}

		return null;
	}

	/**
	 * Get the list of types available
	 *
	 * @return array The list of types
	 *
	 * @since 2.0
	 */
	public function getTypes() {

		if (empty($this->_types)) {

			$this->_types = array();
			$path   = $this->getResource() . '/types';
			$filter = '/^.*config$/';

			if ($files = $this->app->path->files($path, false, $filter)) {
				foreach ($files as $file) {
					$alias = basename($file, '.config');
					$this->_types[$alias] = $this->app->object->create('Type', array($alias, $this));
				}
			}
		}

		return $this->_types;
	}

  	/**
  	 * Get a submission object
  	 *
  	 * @param int $id The id of the submission
  	 *
  	 * @return Submission The submission object
  	 *
  	 * @since 2.0
  	 */
	public function getSubmission($id) {
		$submissions = $this->getSubmissions();

		if (isset($submissions[$id])) {
			return $submissions[$id];
		}

		return null;
	}

	/**
	 * Get the submission object that is configured as the Item Edit Submission
	 *
	 * @return Submission The Item Edit Submission
	 *
	 * @since 2.0
	 */
	public function getItemEditSubmission() {
		foreach ($this->getSubmissions() as $submission) {
			if ($submission->isItemEditSubmission()) {
				return $submission;
			}
		}

		return null;
	}

	/**
	 * Get a list of submissions
	 *
	 * @return array The list of available submissions
	 *
	 * @since 2.0
	 */
	public function getSubmissions() {

		if (empty($this->_submissions)) {
            $this->_submissions = $this->app->table->submission->all(array('conditions' => array('application_id = ' . (int) $this->id)));
		}

		return $this->_submissions;
	}

	/**
	 * Get the parameters for the application
	 *
	 * @param string $for Specify for what use the parameters are for (options: 'site', 'frontpage'). Default: all
	 *
	 * @return ParameterData The parameters requested
	 *
	 * @since 2.0
	 */
	public function getParams($for = null) {

		// get site params
		if ($for == 'site') {

			return $this->app->parameter->create()
				->loadArray($this->params)
				->set('config.', $this->params->get('global.config.'))
				->set('template.', $this->params->get('global.template.'));

		// get frontpage params and inherit globals
		} elseif ($for == 'frontpage') {

			return $this->app->parameter->create()
				->set('config.', $this->params->get('global.config.'))
				->set('template.', $this->params->get('global.template.'))
				->loadArray($this->params);
		}

		return $this->params;
	}

	/**
	 * Get the parameter form object
	 *
	 * @return AppParameterForm The parameter form
	 *
	 * @since 2.0
	 */
	public function getParamsForm() {

		// get parameter xml file
		if ($xml = $this->app->path->path($this->getResource().$this->metaxml_file)) {

            // get form
            $form = $this->app->parameterform->create();
            $params = array($xml);

            // trigger configparams event
            $params = $this->app->event->dispatcher->notify($this->app->event->create($this, 'application:configparams')->setReturnValue($params))->getReturnValue();

            // add config xml files
            foreach ($params as $xml) {
                $form->addXML($xml);
            }

			return $form;
		}

		return null;
	}

	/**
	 * Get the parameter form object from the addons
	 *
	 * @return AppParameterForm The parameter form created by the extensions
	 *
	 * @since 2.0
	 */
	public function getAddonParamsForms() {

		$forms = array();

		// load xml config files
		foreach ($this->app->path->files($this->getResource() . 'config/', false, '/\.xml$/i') as $file) {
			if (($file = $this->app->path->path($this->getResource() . 'config/' . $file)) && ($xml = simplexml_load_file($file))) {
				if ($xml->getName() == 'config') {
					$forms[(string) $xml->name] = $this->app->parameterform->create($file);
				}
			}
		}

		return $forms;
	}

	/**
	 * Get the metadata informations as an array
	 *
	 * @param string $key The meta information to get (default: null, get all the metadata info)
	 *
	 * @return array The metadata informations
	 *
	 * @since 2.0
	 */
	public function getMetaData($key = null) {

		$data = $this->app->data->create();
		$xml  = $this->getMetaXML();

		if (!$xml) {
			return false;
		}

		if ($xml->getName() != 'application') {
			return false;
		}

		$data['name'] 		  = (string) $xml->name;
		$data['creationdate'] = $xml->creationDate ? (string) $xml->creationDate : 'Unknown';
		$data['author'] 	  = $xml->author ? (string) $xml->author : 'Unknown';
		$data['copyright'] 	  = (string) $xml->copyright;
		$data['authorEmail']  = (string) $xml->authorEmail;
		$data['authorUrl']    = (string) $xml->authorUrl;
		$data['version'] 	  = (string) $xml->version;
		$data['description']  = (string) $xml->description;
		$data['license']  	  = (string) $xml->license;

		$data['positions'] = array();
		if (isset($xml->positions)) {
			foreach ($xml->positions->children() as $element) {
				$data['positions'][] = (string) $element;
			}
		}

		return $key == null ? $data : $data->get($key);

	}

	/**
	 * Get the metadata xml
	 *
	 * @return string The xml of the metadata file
	 *
	 * @since 2.0
	 */
	public function getMetaXML() {

		if (empty($this->_metaxml)) {
			$this->_metaxml = simplexml_load_file($this->getMetaXMLFile());
		}

		return $this->_metaxml;
	}

	/**
	 * Get the metadata xml file path
	 *
	 * @return string The path to the metadata xml file
	 *
	 * @since 2.0
	 */
	public function getMetaXMLFile() {
		return $this->getPath() . '/' . $this->metaxml_file;
	}

	/**
	 * Get the informations about an image resource
	 *
	 * @param string $name The name of the image parameter
	 *
	 * @return array The list of informations about the image ('path', 'src', 'mime', 'width', 'height', 'width_height')
	 *
	 * @since 2.0
	 */
	public function getImage($name) {
		if ($image = $this->params->get($name)) {
			return $this->app->html->_('zoo.image', $image, $this->params->get($name . '_width'), $this->params->get($name . '_height'));
		}
		return null;
	}

	/**
	 * Trigger content plugins on a text
	 *
	 * @param string $text The text to trigger the content plugins on
	 *
	 * @return string The text after the triggering of the plugins
	 *
	 * @since 2.0
	 */
	public function getText($text) {
		return $this->app->zoo->triggerContentPlugins($text, array(), 'com_zoo.application.description');
	}

	/**
	 * Add the menu items to the administrator menu for the application
	 *
	 * @param AppMenu $menu The menu object
	 *
	 * @since 2.0
	 */
	public function addMenuItems($menu) {

		// get current controller
		$controller = $this->app->request->getWord('controller');
		$controller = in_array($controller, array('new', 'manager')) ? 'item' : $controller;

		// create application tab
		$tab = $this->app->object->create('AppMenuItem', array($this->id, $this->name, $this->app->link(array('controller' => $controller, 'changeapp' => $this->id))));
		$menu->addChild($tab);

		// menu items
		$items = array(
			'item'          => JText::_('Items'),
			'category'      => JText::_('Categories'),
			'frontpage'     => JText::_('Frontpage'),
			'comment'       => JText::_('Comments'),
			'tag'           => JText::_('Tags'),
            'submission'    => JText::_('Submissions')
		);

		// add menu items
		foreach ($items as $controller => $name) {
			$tab->addChild($this->app->object->create('AppMenuItem', array($this->id.'-'.$controller, $name, $this->app->link(array('controller' => $controller, 'changeapp' => $this->id)))));
		}

		// add config menu item
		$id     = $this->id.'-configuration';
		$link   = $this->app->link(array('controller' => 'configuration', 'changeapp' => $this->id));
		$config = $this->app->object->create('AppMenuItem', array($id, JText::_('Config'), $link));
		$config->addChild($this->app->object->create('AppMenuItem', array($id, JText::_('Application'), $link)));
		$config->addChild($this->app->object->create('AppMenuItem', array($id.'-importexport', JText::_('Import / Export'), $this->app->link(array('controller' => 'configuration', 'changeapp' => $this->id, 'task' => 'importexport')))));
		$tab->addChild($config);

		// trigger event for adding custom menu items
		$this->app->event->dispatcher->notify($this->app->event->create($this, 'application:addmenuitems', array('tab' => &$tab)));

	}

	/**
	 * Check if the comments are enabled in this Application
	 *
	 * @return boolean If the comments are enabled
	 *
	 * @since 2.0
	 */
	public function isCommentsEnabled() {
		return $this->getParams()->get('global.comments.enable_comments', 1);
	}

}

/**
 * Exception for the Application class
 *
 * @see Application
 */
class ApplicationException extends AppException {}