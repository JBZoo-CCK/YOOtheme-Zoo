<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: TagController
		The controller class for tag
*/
class TagController extends AppController {

	public $application;

	public function __construct($default = array()) {
		parent::__construct($default);

		// set table
		$this->table = $this->app->table->tag;

		// get application
		$this->application 	= $this->app->zoo->getApplication();

		// set base url
		$this->baseurl = $this->app->link(array('controller' => $this->controller), false);

	}

	public function display($cachable = false, $urlparams = false) {

		// set toolbar items
		$this->app->system->application->JComponentTitle = $this->application->getToolbarTitle(JText::_('Tags'));
		$this->app->toolbar->deleteList();
		$this->app->zoo->toolbarHelp();

		$this->app->html->_('behavior.tooltip');

		// get request vars
		$state_prefix     = $this->option.'_'.$this->application->id.'.tags.';
		$filter_order	  = $this->app->system->application->getUserStateFromRequest($state_prefix.'filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir = $this->app->system->application->getUserStateFromRequest($state_prefix.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$limit		      = $this->app->system->application->getUserStateFromRequest('global.list.limit', 'limit', $this->app->system->config->get('list_limit'), 'int');
		$limitstart		  = $this->app->system->application->getUserStateFromRequest($state_prefix.'limitstart', 'limitstart', 0,	'int');
		$search	          = $this->app->system->application->getUserStateFromRequest($state_prefix.'search', 'search', '', 'string');
		$search			  = $this->app->string->strtolower($search);

		// is filtered ?
		$this->is_filtered = !empty($search);

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		// get data
		$filter     = ($filter_order) ? $filter_order . ' ' . $filter_order_Dir : '';

		$count = (int) $this->table->count($this->application->id, $search);
		$limitstart = $limitstart > $count ? floor($count / $limit) * $limit : $limitstart;

		$this->tags = $this->table->getAll($this->application->id, $search, '', $filter, $limitstart, $limit);

		$this->pagination = $this->app->pagination->create($count, $limitstart, $limit);

		// table ordering and search filter
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order']     = $filter_order;
		$this->lists['search']    = $search;

		// display view
		$this->getView()->display();
	}

	public function remove() {

		// init vars
		$tags = $this->app->request->get('cid', 'array', array());

		if (count($tags) < 1) {
			$this->app->error->raiseError(500, JText::_('Select a tag to delete'));
		}

		try {

			// delete tags
			$this->table->delete($tags, $this->application->id);

			// set redirect message
			$msg = JText::_('Tag Deleted');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseWarning(0, JText::_('Error Deleting Tag').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function update() {

		// init vars
		$old = $this->app->request->getString('old');
		$new = $this->app->request->getString('new');
		$msg = null;

		try {

			// update tag
			if (!empty($new) && $old != $new) {
				$this->table->update($this->application->id, $old, $new);

				// set redirect message
				$msg = JText::_('Tag Updated Successfully');
			}

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseWarning(0, JText::_('Error Updating Tag').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl, $msg);
	}

}

/*
	Class: TagControllerException
*/
class TagControllerException extends AppException {}