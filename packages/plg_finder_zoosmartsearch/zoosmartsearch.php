<?php
/**
* @package   Smart Search - ZOO
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

defined('JPATH_BASE') or die;

jimport('joomla.application.component.helper');
jimport('joomla.filesystem.file');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

class plgFinderZOOSmartSearch extends FinderIndexerAdapter {

	public $app;

	protected $context = 'ZOO';
	protected $extension = 'com_zoo';
	protected $layout = 'item';
	protected $type_title = 'ZOO Item';
	protected $table = '#__zoo_item';
	protected $state_field = 'state';

	public function __construct(&$subject, $config)	{

		// load ZOO config
		jimport('joomla.filesystem.file');
		if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php') || !JComponentHelper::getComponent('com_zoo', true)->enabled) {
			return;
		}
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// Get the ZOO App instance
		$this->app = App::getInstance('zoo');

		parent::__construct($subject, $config);

		// load zoo frontend language file
		$this->app->system->language->load('com_zoo');

	}

	protected function index(FinderIndexerResult $item, $format = 'html') {

		// Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false || !$item->id) {
			return;
		}

		if (!$zoo_item = $this->app->table->item->get($item->id, true)) {
			return;
		}

		$registry = new JRegistry;
		$registry->loadArray($zoo_item->getParams()->get("metadata."));
		$item->metadata = $registry;

		$item->metaauthor = $zoo_item->getParams()->get("metadata.author");

		$item->addInstruction(FinderIndexer::META_CONTEXT, 'link');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'created_by_alias');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'element_data');

		$item->summary = $this->renderer->render('item.default', array('item' => $zoo_item));
		$item->url = $this->getURL($item->id, $this->extension, $this->layout);
		$item->route = $this->app->route->item($zoo_item, false);
		$item->path = FinderIndexerHelper::getContentPath($item->route);
		$item->state = ($zoo_item->searchable == 1) && ($zoo_item->state == 1);

		$item->element_data = $this->app->database->queryResultArray('SELECT value FROM '.ZOO_TABLE_SEARCH.' WHERE item_id = '.(int) $item->id);

		$item->addTaxonomy('Type', $zoo_item->getType()->name);

		foreach ($zoo_item->getRelatedCategories(true) as $category) {
			$item->addTaxonomy('Category', $category->name);
		}

		foreach ($zoo_item->getTags() as $tag) {
			$item->addTaxonomy('Tag', $tag);
		}

		FinderIndexerHelper::getContentExtras($item);

        if ($this->app->joomla->version->isCompatible('3.0')) {
            $this->indexer->index($item);
        } else {
            FinderIndexer::index($item);
        }

	}

	protected function setup() {

		// workaround to make sure JSite is loaded
		$this->app->loader->register('JSite', 'root:includes/application.php');
        
		$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('plugins:finder/zoosmartsearch/')));

		return true;
	}

	protected function getListQuery($sql = null) {

		$db = JFactory::getDbo();

		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);
		$sql->select('a.id, a.name AS title, a.alias');
		$sql->select('a.created_by_alias, a.modified, a.modified_by');
		$sql->select('a.publish_up AS publish_start_date, a.publish_down AS publish_end_date');
		$sql->select('a.access, a.state, a.searchable');
		$sql->from("$this->table AS a");

		return $sql;

	}

	protected function getStateQuery() {
		$sql = $this->db->getQuery(true);
		$sql->select('a.id, a.state, a.access, a.searchable');
		$sql->from($this->table . ' AS a');

		return $sql;
	}

	public function onFinderAfterSave($context, $row) {
		if ($context == $this->app->component->self->name.'.item') {
			$this->reindex($row->id);
		}

		return true;
	}

	public function onFinderAfterDelete($context, $table) {
		if ($context == $this->app->component->self->name.'.item') {
			$id = $table->id;
		} elseif ($context == 'com_finder.index') {
			$id = $table->link_id;
		} else {
			return true;
		}

		return $this->remove((int) $id);
	}

	public function registerZOOEvents() {
		if ($this->app) {
			$this->app->event->dispatcher->connect('type:assignelements', array($this, 'assignElements'));
		}
	}

	public function assignElements() {
		$this->app->system->application->enqueueMessage(JText::_('Only text based elements are allowed in the search layouts'), 'notice');
	}

}
