<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ItemController
		The controller class for item
*/
class ItemController extends AppController {

	public $application;

	const MAX_MOST_USED_TAGS = 8;

	public function __construct($default = array()) {
		parent::__construct($default);

		// set table
		$this->table = $this->app->table->item;

		// get application
		$this->application 	= $this->app->zoo->getApplication();

		// set base url
		$this->baseurl = $this->app->link(array('controller' => $this->controller), false);

		// set user
		$this->user = $this->app->user->get();

		// register tasks
		$this->registerTask('element', 'display');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}

	public function display($cachable = false, $urlparams = false) {

		// get app from Request (currently used in zooapplication element)
		if ($id = $this->app->request->getInt('app_id')) {
			$this->application = $this->app->table->application->get($id);
		}

		// get database
		$this->db = $this->app->database;

		// set toolbar items
		$canCreate    = false;
		$canDelete    = false;
		$canEditState = false;

		foreach ($this->application->getTypes() as $type) {
			if ($type->canCreate()) {
				$canCreate = true;
			}
			if ($type->canDelete()) {
				$canDelete = true;
			}
			if ($type->canEditState()) {
				$canEditState = true;
			}
		}
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Items'));
		if ($canCreate) {
			$this->app->toolbar->addNew();
		}
		$this->app->toolbar->editList();
		if ($canEditState) {
			$this->app->toolbar->publishList();
			$this->app->toolbar->unpublishList();
		}
		if ($this->application->canManageFrontpage()) {
			$this->app->toolbar->custom('togglefrontpage', 'checkin', 'checkin', 'Toggle Frontpage', true);
		}
		if ($canCreate) {
			$this->app->toolbar->custom('docopy', 'copy.png', 'copy_f2.png', 'Copy');
		}
		if ($canDelete) {
			$this->app->toolbar->deleteList();
		}

		$this->app->zoo->toolbarHelp();

		$this->app->html->_('behavior.tooltip');

		// get request vars
		$this->filter_item	= $this->app->request->getInt('item_filter', 0);
		$this->type_filter	= $this->app->request->get('type_filter', 'array', array());
		$state_prefix       = $this->option.'_'.$this->application->id.'.'.($this->getTask() == 'element' ? 'element' : 'item').'.';
		$filter_order	    = $this->app->system->application->getUserStateFromRequest($state_prefix.'filter_order', 'filter_order', 'a.created', 'cmd');
		$filter_order_Dir   = $this->app->system->application->getUserStateFromRequest($state_prefix.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$filter_category_id = $this->app->system->application->getUserStateFromRequest($state_prefix.'filter_category_id', 'filter_category_id', '-1', 'string');
		$limit		        = $this->app->system->application->getUserStateFromRequest('global.list.limit', 'limit', $this->app->system->config->get('list_limit'), 'int');
		$limitstart			= $this->app->system->application->getUserStateFromRequest($state_prefix.'limitstart', 'limitstart', 0,	'int');
		$filter_type     	= $this->app->system->application->getUserStateFromRequest($state_prefix.'filter_type', 'filter_type', '', 'string');
		$filter_author_id   = $this->app->system->application->getUserStateFromRequest($state_prefix.'filter_author_id', 'filter_author_id', 0, 'int');
		$search	            = $this->app->system->application->getUserStateFromRequest($state_prefix.'search', 'search', '', 'string');
		$search			    = $this->app->string->strtolower($search);

		// is filtered ?
		$this->is_filtered = $filter_category_id <> '-1' || !empty($filter_type) || !empty($filter_author_id) || !empty($search);

		$this->users  = $this->table->getUsers($this->application->id);
		$this->groups = $this->app->zoo->getGroups();

		// select
		$select = 'a.*, EXISTS (SELECT true FROM '.ZOO_TABLE_CATEGORY_ITEM.' WHERE item_id = a.id AND category_id = 0) as frontpage';

		// get from
		$from = $this->table->name.' AS a';

		// get data from the table
		$where = array();

		// application filter
		$where[] = 'a.application_id = ' . (int) $this->application->id;

		// category filter
		if ($filter_category_id === '') {
			$from   .= ' LEFT JOIN '.ZOO_TABLE_CATEGORY_ITEM.' AS ci ON a.id = ci.item_id';
			$where[] = 'ci.item_id IS NULL';
        } else if ($filter_category_id > -1) {
			$from   .= ' LEFT JOIN '.ZOO_TABLE_CATEGORY_ITEM.' AS ci ON a.id = ci.item_id';
			$where[] = 'ci.category_id = ' . (int) $filter_category_id;
		}

		// type filter
		if (!empty($this->type_filter)) {
			$where[] = 'a.type IN ("' . implode('", "', $this->type_filter) . '")';
		} else if (!empty($filter_type)) {
			$where[] = 'a.type = "' . (string) $filter_type . '"';
		}

		// item filter
		if ($this->filter_item > 0) {
			$where[] = 'a.id != ' . (int) $this->filter_item;
		}

		// author filter
		if ($filter_author_id > 0) {
			$where[] = 'a.created_by = ' . (int) $filter_author_id;
		}

		if ($search) {
			$from   .= ' LEFT JOIN '.ZOO_TABLE_TAG.' AS t ON a.id = t.item_id';
			$where[] = '(LOWER(a.name) LIKE '.$this->db->Quote('%'.$this->db->escape($search, true).'%', false)
				. ' OR LOWER(t.name) LIKE '.$this->db->Quote('%'.$this->db->escape($search, true).'%', false)
				. ' OR LOWER(a.alias) LIKE '.$this->db->Quote('%'.$this->db->escape($search, true).'%', false) . ')';
		}

		$options = array(
            'select' => 'a.id',
			'from' =>  $from,
			'conditions' => array(implode(' AND ', $where)),
			'group' => 'a.id');

		$count = $this->table->count($options);

		$options['select'] = $select;
        $options['order'] = $filter_order.' '.$filter_order_Dir;

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$limitstart = $limitstart > $count ? floor($count / $limit) * $limit : $limitstart;

		$this->items = $this->table->all($limit > 0 ? array_merge($options, array('offset' => $limitstart, 'limit' => $limit)) : $options);
		$this->items = array_merge($this->items);

		$this->pagination = $this->app->pagination->create($count, $limitstart, $limit);

		// category select
		$options = array();
        $options[] = $this->app->html->_('select.option', '-1', '- ' . JText::_('Select Category') . ' -');
        $options[] = $this->app->html->_('select.option', '', '- ' . JText::_('uncategorized') . ' -');
		$options[] = $this->app->html->_('select.option', '0', '- '.JText::_('Frontpage'));
		$this->lists['select_category'] = $this->app->html->_('zoo.categorylist', $this->application, $options, 'filter_category_id', 'class="inputbox auto-submit"', 'value', 'text', $filter_category_id);

		// type select
		$options = array($this->app->html->_('select.option', '0', '- '.JText::_('Select Type').' -'));
		$this->lists['select_type'] = $this->app->html->_('zoo.typelist', $this->application, $options, 'filter_type', 'class="inputbox auto-submit"', 'value', 'text', $filter_type, false, false, $this->type_filter);

		// author select
		$options = array($this->app->html->_('select.option', '0', '- '.JText::_('Select Author').' -'));
		$this->lists['select_author'] = $this->app->html->_('zoo.itemauthorlist',  $options, 'filter_author_id', 'class="inputbox auto-submit"', 'value', 'text', $filter_author_id);

		// table ordering and search filter
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order']	  = $filter_order;
		$this->lists['search']    = $search;

		// display view
		$layout = $this->getTask() == 'element' ? 'element' : 'default';
		$this->getView()->setLayout($layout)->display();
	}

	public function loadtags() {

		// get request vars
		$tag = $this->app->request->getString('tag', '');

		echo $this->app->tag->loadTags($this->application->id, $tag);

	}

	public function add() {

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Item') .': <small><small>[ '.JText::_('New').' ]</small></small>');
		$this->app->toolbar->cancel();

		// get types
		$this->types = array();
		foreach ($this->application->getTypes() as $name => $type) {
			if ($this->app->user->canCreate(null, $type->getAssetName())) {
				$this->types[$name] = $type;
			}
		}

		// no types available ?
		if (count($this->types) == 0) {
			$this->app->error->raiseNotice(0, JText::_('Please create a type first.'));
			$this->app->system->application->redirect($this->app->link(array('controller' => 'manager', 'task' => 'types', 'group' => $this->application->application_group), false));
		}

		// only one type ? then skip type selection
		if (count($this->types) == 1) {
			$type = array_shift($this->types);
			$this->app->system->application->redirect($this->baseurl.'&task=edit&type='.$type->id);
		}

		// display view
		$this->getView()->setLayout('add')->display();
	}

	public function edit() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// get request vars
		$cid  = $this->app->request->get('cid.0', 'int');
		$edit = $cid > 0;

		// get item
		if ($edit) {
			if (!$this->item = $this->app->table->item->get($cid)) {
				$this->app->error->raiseError(500, JText::sprintf('Unable to access item with id %s', $cid));
				return;
			}

			// check ACL
			if (!$this->item->canEdit()) {
				throw new ItemControllerException("Invalid access permissions", 1);
			}
		} else {
			$this->item = $this->app->object->create('Item');
			$this->item->application_id = $this->application->id;
			$this->item->type = $this->app->request->getVar('type');
			$this->item->publish_down = $this->app->database->getNullDate();
			$this->item->access = $this->app->joomla->getDefaultAccess();

			// check ACL
			if (!$this->item->canCreate()) {
				throw new ItemControllerException("Invalid access permissions", 1);
			}
		}

		// get item params
		$this->params = $this->item->getParams();

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Item').': '.$this->item->name.' <small><small>[ '.($edit ? JText::_('Edit') : JText::_('New')).' ]</small></small>');
		$this->app->toolbar->apply();
		$this->app->toolbar->save();
		$this->app->toolbar->save2new();
		if ($edit) {
			$this->app->toolbar->save2copy();
		}
		$this->app->toolbar->cancel('cancel', $edit ? 'Close' : 'Cancel');
		$this->app->zoo->toolbarHelp();

		// published select
		$this->lists['select_published'] = $this->app->html->_('select.booleanlist', 'state', null, $this->item->state);

		// published searchable
		$this->lists['select_searchable'] = $this->app->html->_('select.booleanlist', 'searchable', null, $this->item->searchable);

		// categories select
		$related_categories = $this->item->getRelatedCategoryIds();
		$this->lists['select_frontpage']  = $this->app->html->_('select.booleanlist', 'frontpage', null, in_array(0, $related_categories));
		$this->lists['select_categories'] = count($this->application->getCategoryTree()) > 1 ?
				$this->app->html->_('zoo.categorylist', $this->application, array(), 'categories[]', 'size="15" multiple="multiple" data-no_results_text="'.JText::_('No results match0r').'" data-placeholder="'.JText::_('Select Category').'"', 'value', 'text', $related_categories, false, false, 0 ,'<sup>|_</sup>&nbsp;', '.&nbsp;&nbsp;&nbsp;', '')
				: '<a href="'.$this->app->link(array('controller' => 'category'), false).'" >'.JText::_('Please add categories first').'</a>';
		$this->lists['select_primary_category'] = $this->app->html->_('zoo.categorylist', $this->application, array($this->app->html->_('select.option', '', JText::_('COM_ZOO_NONE'))), 'params[primary_category]', 'data-no_results_text="'.JText::_('No results match').'"', 'value', 'text', $this->params->get('config.primary_category'), false, false, 0 ,'<sup>|_</sup>&nbsp;', '.&nbsp;&nbsp;&nbsp;', '');
		// most used tags
		$this->lists['most_used_tags'] = $this->app->table->tag->getAll($this->application->id, null, null, 'items DESC, a.name ASC', null, self::MAX_MOST_USED_TAGS);

		// comments enabled select
		$this->lists['select_enable_comments'] = $this->app->html->_('select.booleanlist', 'params[enable_comments]', null, $this->params->get('config.enable_comments', 1));

		// display view
		$this->getView()->setLayout('edit')->display();
	}

	public function save() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$now        = $this->app->date->create();
		$frontpage  = $this->app->request->getBool('frontpage', false);
		$categories	= $this->app->request->get('categories', null);
		$details	= $this->app->request->get('details', null);
		$cid        = $this->app->request->get('cid.0', 'int');
		$tzoffset   = $this->app->date->getOffset();
		$post       = array_merge($this->app->request->get('post:', 'array', array()), $details);

		try {

			// get item
			if ($cid) {
				$item = $this->table->get($cid);
			} else {
				$item = $this->app->object->create('Item');
				$item->application_id = $this->application->id;
				$item->type = $this->app->request->getVar('type');
			}

			// bind item data
			self::bind($item, $post, array('elements', 'params', 'created_by'));
            $created_by = isset($post['created_by']) ? $post['created_by'] : '';
            $item->created_by = empty($created_by) ? $this->app->user->get()->id : $created_by == 'NO_CHANGE' ? $item->created_by : $created_by;
			$tags = isset($post['tags']) ? $post['tags'] : array();
			$item->setTags($tags);

			// bind element data
			$item->elements = $this->app->data->create();
			foreach ($item->getElements() as $id => $element) {
				if (isset($post['elements'][$id])) {
					$element->bindData($post['elements'][$id]);
				} else {
					$element->bindData();
				}
			}

			// set alias
			if (!strlen(trim($item->alias))) {
				$item->alias = $this->app->string->sluggify($item->name);
			}
			$item->alias = $this->app->alias->item->getUniqueAlias($item->id, $this->app->string->sluggify($item->alias));

			// set modified
			$item->modified	   = $now->toSQL();
			$item->modified_by = $this->user->get('id');

			// set created date
			try {
                $item->created = $this->app->date->create($item->created, $tzoffset)->toSQL();
            } catch (Exception $e) {
                $item->created = $this->app->date->create()->toSQL();
            }

			// set publish up date
            try {
                $item->publish_up = $this->app->date->create($item->publish_up, $tzoffset)->toSQL();
            } catch (Exception $e) {
                $item->publish_up = $this->app->date->create()->toSQL();
            }

			// set publish down date
            try {
                $item->publish_down = $this->app->date->create($item->publish_down, $tzoffset)->toSQL();
            } catch (Exception $e) {
                $item->publish_down = $this->app->database->getNullDate();
            }

			// get primary category
			$primary_category = @$post['params']['primary_category'];
			if (empty($primary_category) && count($categories)) {
				$primary_category = $categories[0];
			}

			// set params
			$item->getParams()
				->remove('metadata.')
				->remove('template.')
				->remove('content.')
				->remove('config.')
				->set('metadata.', @$post['params']['metadata'])
				->set('template.', @$post['params']['template'])
				->set('content.', @$post['params']['content'])
				->set('config.', @$post['params']['config'])
				->set('config.enable_comments', @$post['params']['enable_comments'])
				->set('config.primary_category', $primary_category);

			// save item
			$this->table->save($item);

			// make sure categories contain primary category
			if (!empty($primary_category) && !in_array($primary_category, $categories)) {
				$categories[] = $primary_category;
			}

			// save category relations
			if ($frontpage) {
				$categories[] = 0;
			}
			$this->app->category->saveCategoryItemRelations($item, $categories);

			// set redirect message
			$msg = JText::_('Item Saved');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Saving Item').' ('.$e.')');
			$this->_task = 'apply';
			$msg = null;

		}

		$link = $this->baseurl;
		switch ($this->getTask()) {
			case 'save2copy' :
			case 'apply' :
				$link .= '&task=edit&type='.$item->type.'&cid[]='.$item->id;
				break;
			case 'save2new' :
				$link .= '&task=add';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	public function save2copy() {
		$this->app->request->set('cid.0', 0)
			->set('id', 0)
			->set('name', $this->app->request->get('name', 'string').' ('.JText::_('Copy').')');
		$this->save();
	}

	public function docopy() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$now  = $this->app->date->create()->toSQL();
		$cid  = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a item to copy'));
		}

		try {

			// copy items
			foreach ($cid as $id) {

				// get item
				$item       = $this->table->get($id);
				$categories = $item->getRelatedCategoryIds();

				// copy item
				$item->id          = 0;                         						// set id to 0, to force new item
				$item->name       .= ' ('.JText::_('Copy').')'; 						// set copied name
				$item->alias       = $this->app->alias->item->getUniqueAlias($id, $item->alias.'-copy'); 	// set copied alias
				$item->state       = 0;                         						// unpublish item
				$item->created	   = $item->modified = $now;
				$item->created_by  = $item->modified_by = $this->user->get('id');
				$item->hits		   = 0;

				// copy tags
				$item->setTags($this->app->table->tag->getItemTags($id));

				// save copied item/element data
				$this->table->save($item);

				// save category relations
				$this->app->category->saveCategoryItemRelations($item, $categories);
			}

			// set redirect message
			$msg = JText::_('Item Copied');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Copying Item').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function remove() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a item to delete'));
		}

		try {

			// delete items
			foreach ($cid as $id) {
				$this->table->delete($this->table->get($id));
			}

			// set redirect message
			$msg = JText::_('Item Deleted');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseWarning(0, JText::_('Error Deleting Item').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function savepriority() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$msg      = JText::_('Order Priority saved');
		// init vars
		$priority = $this->app->request->get('priority', 'array', array());

		try {

			// update the priority for items
			foreach ($priority as $id => $value) {
				$item = $this->table->get((int) $id);

				// only update, if changed
				if ($item->priority != $value) {
					$item->priority = $value;
					$this->table->save($item);
				}
			}

			// set redirect message
			$msg = json_encode(array(
				'group' => 'info',
				'title' => JText::_('Success!'),
				'text'  => JText::_('Item Priorities Saved')));

		} catch (AppException $e) {

			// raise error on exception
			$msg = json_encode(array(
				'group' => 'error',
				'title' => JText::_('Error!'),
				'text'  => JText::_('Error editing item priority').' ('.$e.')'));

		}

		echo $msg;
	}

	public function resethits() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$msg = null;
		$cid = $this->app->request->get('cid.0', 'int');

		try {

			// get item
			$item = $this->table->get($cid);

			// reset hits
			if ($item->hits > 0) {
				$item->hits = 0;

				// save item
				$this->table->save($item);

				// set redirect message
				$msg = JText::_('Item Hits Reseted');
			}

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error Reseting Item Hits').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl.'&task=edit&cid[]='.$item->id, $msg);
	}

	public function publish() {
		$this->_editState(1);
	}

	public function unpublish() {
		$this->_editState(0);
	}

	public function makeSearchable() {
		$this->_editSearchable(1);
	}

	public function makeNoneSearchable() {
		$this->_editSearchable(0);
	}

	public function enableComments() {
		$this->_editComments(1);
	}

	public function disableComments() {
		$this->_editComments(0);
	}

	protected function _editState($state) {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select an item to edit publish state'));
		}

		try {

			// update item state
			foreach ($cid as $id) {
				$this->table->get($id)->setState($state, true);
			}

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error editing Item Published State').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

	protected function _editSearchable($searchable) {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select an item to edit searchable state'));
		}

		try {

			// update item searchable
			foreach ($cid as $id) {
				$item = $this->table->get($id);
				$item->searchable = $searchable;
				$this->table->save($item);
			}

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error editing Item Searchable State').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

	protected function _editComments($enabled) {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select an item to enable/disable comments'));
		}

		try {

			// update item comments
			foreach ($cid as $id) {
				$item = $this->table->get($id);

				$item->params = $item
					->getParams()
					->set('config.enable_comments', $enabled);

				$this->table->save($item);
			}

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error enabling/disabling Item Comments').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

	public function toggleFrontpage() {

		// check for request forgeries
		$this->app->session->checkToken() or jexit('Invalid Token');

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('Select an item to toggle item frontpage setting'));
		}

		try {

			// toggle item frontpage
			foreach ($cid as $id) {
				$item = $this->table->get($id);

				$categories = $item->getRelatedCategoryIds();
				if (($key = array_search('0', $categories, true)) !== false) {
					unset($categories[$key]);
				} else {
					array_push($categories, '0');
				}

				$this->app->category->saveCategoryItemRelations($item, $categories);

			}

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::_('Error toggling item frontpage setting').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);

	}

	public function callElement() {

		// get request vars
		$element_identifier = $this->app->request->getString('elm_id', '');
		$item_id			= $this->app->request->getInt('item_id', 0);
		$type	 			= $this->app->request->getString('type', '');
		$this->method 		= $this->app->request->getCmd('method', '');
		$this->args       	= $this->app->request->getVar('args', array(), 'default', 'array');

		JArrayHelper::toString($this->args);

		// load element
		if ($item_id) {
			$item = $this->table->get($item_id);
		} elseif (!empty($type)) {
			$item = $this->app->object->create('Item');
			$item->application_id = $this->application->id;
			$item->type = $type;
		} else {
			return;
		}

		// execute callback method
		if ($element = $item->getElement($element_identifier)) {
			echo $element->callback($this->method, $this->args);
		}

	}

}

/*
	Class: ItemControllerException
*/
class ItemControllerException extends AppException {}