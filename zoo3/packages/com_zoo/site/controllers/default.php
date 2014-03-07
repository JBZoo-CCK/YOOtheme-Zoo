<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: DefaultController
		Site controller class
*/
class DefaultController extends AppController {

	public $application;

 	/*
		Function: Constructor

		Parameters:
			$default - Array

		Returns:
			DefaultController
	*/
	public function __construct($default = array()) {
		parent::__construct($default);

		// get application
		$this->application = $this->app->zoo->getApplication();

		// get Joomla application
		$this->joomla = $this->app->system->application;

		// get params
		$this->params = $this->joomla->getParams();

		// get pathway
		$this->pathway = $this->joomla->getPathway();

		// registers tasks
		$this->registerTask('frontpage', 'category');
	}

	/*
	 	Function: display
			View method for MVC based architecture

		Returns:
			Void
	*/
	public function display($cachable = false, $urlparams = false) {

		// execute task
		$this->taskMap['display'] = null;
		$this->taskMap['__default'] = null;
		$this->execute($this->app->request->getCmd('view'));
	}

	/*
	 	Function: callElement
			Element callback method

		Returns:
			Void
	*/
	public function callElement() {

		// get request vars
		$element = $this->app->request->getCmd('element', '');
		$method  = $this->app->request->getCmd('method', '');
		$args    = $this->app->request->getVar('args', array(), 'default', 'array');
		$item_id = (int) $this->app->request->getInt('item_id', 0);

		// get user
		$user = $this->app->user->get();

		// get item
		$item = $this->app->table->item->get($item_id);

		// raise 404 if item does not exist or is not published
		if (empty($item) || !$item->isPublished()) {
			return $this->app->error->raiseError(404, JText::_('Item not found'));
		}

		// raise warning when item can not be accessed
		if (!$item->canAccess($user)) {
			return $this->app->error->raiseError(403, JText::_('Unable to access item'));
		}

		// get element and execute callback method
		if ($element = $item->getElement($element)) {
			$element->callback($method, $args);
		}
	}

	public function item() {

		// get request vars
		$item_id = (int) $this->app->request->getInt('item_id', $this->params->get('item_id', 0));

		// get item
		$this->item = $this->app->table->item->get($item_id);

		// get user
		$user = $this->app->user->get();

		// raise 404 if item does not exist or is not published
		if (empty($this->item) || !$this->item->isPublished()) {
			return $this->app->error->raiseError(404, JText::_('Item not found'));
		}

		// raise 403 if user is not allowed to view item
		if (!$this->item->canAccess($user)) {

			// Show error message if logged in and cannot access item
			if ($user->id) {
				return $this->app->error->raiseWarning(403, JText::_('Unable to access item'));
			}

			// redirect to login for guest users
			$return = urlencode(base64_encode($this->app->route->item($this->item, false)));
			$link   = JRoute::_(sprintf('index.php?option=com_users&view=login&return=%s', $return), false);

			$this->setRedirect($link, JText::_('Unable to access item'), 'error');
			return $this->redirect();
		}

		// add canonical
		if ($this->app->system->document instanceof JDocumentHTML) {
			$this->app->system->document->addHeadLink(JRoute::_($this->app->route->item($this->item, false), true, -1), 'canonical');
			$headData = $this->app->system->document->getHeadData();
				foreach ($headData['links'] as $key => $value) {
					if($value['relation'] == 'canonical' && $key != JRoute::_($this->app->route->item($this->item, false), true, -1)) {
						unset($headData['links'][$key]);
				}
			}
			$this->app->system->document->setHeadData($headData);
		}

		// get category_id
		$category_id = (int) $this->app->request->getInt('category_id', $this->item->getPrimaryCategoryId());

		// create item pathway
		$itemid = $this->params->get('item_id');
		if ($this->item->id != $itemid) {
			$categories = $this->application->getCategoryTree(true);
			if (isset($categories[$category_id])) {
				$category = $categories[$category_id];
				$addpath = false;
				$catid   = $this->params->get('category');
				foreach ($category->getPathway() as $cat) {
					if (!$catid || $addpath) {
						$link = JRoute::_($this->app->route->category($cat));
						$this->pathway->addItem($cat->name, $link);
					}
					if ($catid && $catid == $cat->id) {
						$addpath = true;
					}
				}
			}

			$this->pathway->addItem($this->item->name, $this->app->route->item($this->item));
		}

		// update hit count
		$this->item->hit();

		// get page title, if exists
		$title = $this->item->getParams()->get('metadata.title');
		$title = empty($title) ? $this->item->name : $title;
		if ($menu = $this->app->menu->getActive() and @$menu->query['view'] == 'item' and $this->app->parameter->create($menu->params)->get('item_id') == $itemid) {
			if ($page_title = $this->app->parameter->create($menu->params)->get('page_title')) {
				$title = $page_title;
			}
		}

	 	// set metadata
		$this->app->document->setTitle($this->app->zoo->buildPageTitle($title));
		if ($this->app->system->config->get('MetaAuthor')) $this->app->document->setMetadata('author', $this->item->getAuthor());
		if ($description = $this->item->getParams()->get('metadata.description')) $this->app->document->setDescription($description);
		foreach (array('keywords', 'author', 'robots') as $meta) {
			if ($value = $this->item->getParams()->get("metadata.$meta")) $this->app->document->setMetadata($meta, $value);
		}

		// get template and params
		if (!$this->template = $this->application->getTemplate()) {
			return $this->app->error->raiseError(500, JText::_('No template selected'));
		}
		$this->params   = $this->item->getParams('site');

		// set renderer
		$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->template->getPath()));

		// display view
		$this->getView('item')->addTemplatePath($this->template->getPath())->setLayout('item')->display();
	}

    public function submission() {

        // perform the request task
		$this->request->set('task', $this->request->get('layout', ''));
		$this->app->dispatch('submission');

    }

	public function category() {

		// get request vars
		$page        = $this->app->request->getInt('page', 1);
		$category_id = (int) $this->app->request->getInt('category_id', $this->params->get('category'));

		// init vars
		$this->categories = $this->application->getCategoryTree(true, $this->app->user->get(), true);

		// raise 404 if category does not exist
		if ($category_id && !$this->app->table->category->get($category_id)) {
			return $this->app->error->raiseError(404, JText::_('Category not found'));
		}

		// raise warning when category can not be accessed
		if (!isset($this->categories[$category_id])) {
			return $this->app->error->raiseError(403, JText::_('Unable to access category'));
		}

		$this->category   = $this->categories[$category_id];
		$params	          = $category_id ? $this->category->getParams('site') : $this->application->getParams('frontpage');
		$this->item_order = $params->get('config.item_order');
		$layout 		  = $category_id == 0 ? 'frontpage' : 'category';
		$items_per_page   = $params->get('config.items_per_page', 15);
		$offset			  = max(($page - 1) * $items_per_page, 0);

		// get categories and items
		$this->items      = $this->app->table->item->getByCategory($this->application->id, $category_id, true, null, $this->item_order, $offset, $items_per_page);
		$item_count		  = $this->category->id == 0 ? $this->app->table->item->getItemCountFromCategory($this->application->id, $category_id, true) : $this->category->itemCount();

		// set categories to display
		$this->selected_categories = $this->category->getChildren();

		// get item pagination
		$this->pagination = $this->app->pagination->create($item_count, $page, $items_per_page, 'page', 'app');
		$this->pagination->setShowAll($items_per_page == 0);
		$this->pagination_link = $layout == 'category' ? $this->app->route->category($this->category, false) : $this->app->route->frontpage($this->application->id);

		// create pathway
		$addpath = false;
		$catid   = $this->params->get('category');
		foreach ($this->category->getPathway() as $cat) {
			if (!$catid || $addpath) {
				$this->pathway->addItem($cat->name, $this->app->route->category($cat));
			}
			if ($catid && $catid == $cat->id) {
				$addpath = true;
			}
		}

		// get metadata
		$title		 = $params->get('metadata.title') ? $params->get('metadata.title') : ($category_id ? $this->category->name : '');
		$description = $params->get('metadata.description');
		$keywords    = $params->get('metadata.keywords');

		if ($menu = $this->app->menu->getActive() and in_array(@$menu->query['view'], array('category', 'frontpage')) and $menu_params = $this->app->parameter->create($menu->params) and $menu_params->get('category') == $category_id) {

			if ($page_title = $menu_params->get('page_title') or $page_title = $menu->title) {
				$title = $page_title;
			}

			if ($page_description = $menu_params->get('menu-meta_description')) {
				$description = $page_description;
			}

			if ($page_keywords = $menu_params->get('menu-meta_keywords')) {
				$keywords = $page_keywords;
			}

		}

		// set page title
		if ($title) {
			$this->app->document->setTitle($this->app->zoo->buildPageTitle($title));
		}

		if ($description) {
			$this->app->document->setDescription($description);
		}

		if ($keywords) {
			$this->app->document->setMetadata('keywords', $keywords);
		}

		// set metadata
		foreach (array('author', 'robots') as $meta) {
			if ($value = $params->get("metadata.$meta")) $this->app->document->setMetadata($meta, $value);
		}

		// add feed links
		if ($params->get('config.show_feed_link') && $this->app->system->document instanceof JDocumentHTML) {
			if ($alternate = $params->get('config.alternate_feed_link')) {
				$this->app->document->addHeadLink($alternate, 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
			} else {
				$this->app->document->addHeadLink(JRoute::_($this->app->route->feed($this->category, 'rss')), 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
				$this->app->document->addHeadLink(JRoute::_($this->app->route->feed($this->category, 'atom')), 'alternate', 'rel', array('type' => 'application/atom+xml', 'title' => 'Atom 1.0'));
			}
		}

		// set alphaindex
		if ($params->get('template.show_alpha_index')) {
			$this->alpha_index = $this->_getAlphaindex();
		}

		// set template and params
		if (!$this->template = $this->application->getTemplate()) {
			return $this->app->error->raiseError(500, JText::_('No template selected'));
		}
		$this->params   = $params;

		// set renderer
		$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->template->getPath()));

		// display view
		$this->getView($layout)->addTemplatePath($this->template->getPath())->setLayout($layout)->display();
	}

	public function alphaindex() {

		// get request vars
		$page             = $this->app->request->getInt('page', 1);
		$this->alpha_char = $this->app->request->getString('alpha_char', '');

		// get params
		$params 	      = $this->application->getParams('site');
		$items_per_page   = $params->get('config.items_per_page', 15);
		$this->item_order = $params->get('config.item_order');
		$add_alpha_index  = $params->get('config.alpha_index', 0);

		// get categories
		$this->categories = $add_alpha_index == 1 || $add_alpha_index == 3 ? $this->application->getCategoryTree(true, $this->app->user->get(), true) : array();

		// set alphaindex
		$this->alpha_index = $this->_getAlphaindex();
		$this->alpha_char = empty($this->alpha_char) ? $this->alpha_index->getOther() : $this->alpha_index->getChar($this->alpha_char);

		// get items
		$this->items = array();
		if ($add_alpha_index == 2 || $add_alpha_index == 3) {
			$table = $this->app->table->item;
			if ($this->alpha_char == $this->alpha_index->getOther()) {
				$this->items = $table->getByCharacter($this->application->id, $this->alpha_index->getIndex(), true, true, null, $this->item_order);
			} else {
				$this->items = $table->getByCharacter($this->application->id, $this->alpha_char, false, true, null, $this->item_order);
			}
		}

		// get item pagination
		$this->pagination = $this->app->pagination->create(count($this->items), $page, $items_per_page, 'page', 'app');
		$this->pagination->setShowAll($items_per_page == 0);
		$this->pagination_link = $this->app->route->alphaindex($this->application->id, $this->alpha_char);

		// slice out items
		if (!$this->pagination->getShowAll()) {
			$this->items = array_slice($this->items, $this->pagination->limitStart(), $items_per_page);
		}

		// set category and categories to display
		if (isset($this->categories[0])) {
			$this->category = $this->categories[0];
		}
		$this->selected_categories = $this->alpha_index->getObjects($this->alpha_char, 'category');

		// create pathway
		$this->pathway->addItem(JText::_('Alpha Index'), JRoute::_($this->app->route->alphaindex($this->application->id, $this->alpha_char)));

		// set template and params
		if (!$this->template = $this->application->getTemplate()) {
			return $this->app->error->raiseError(500, JText::_('No template selected'));
		}
		$this->params   = $params;

		// set renderer
		$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->template->getPath()));

		// display view
		$this->getView('alphaindex')->addTemplatePath($this->template->getPath())->setLayout('alphaindex')->display();
	}

	public function tag() {

		// get request vars
		$page      = $this->app->request->getInt('page', 1);
		$this->tag = $this->app->request->getString('tag', '');

		// get params
		$params 	 	  = $this->application->getParams('site');
		$items_per_page   = $params->get('config.items_per_page', 15);
		$this->item_order = $params->get('config.item_order');

		// get categories and items
		$this->categories = $this->application->getCategoryTree(true);
		$this->items = $this->app->table->item->getByTag($this->application->id, $this->tag, true, null, $this->item_order);

		// get item pagination
		$this->pagination = $this->app->pagination->create(count($this->items), $page, $items_per_page, 'page', 'app');
		$this->pagination->setShowAll($items_per_page == 0);
		$this->pagination_link = $this->app->route->tag($this->application->id, $this->tag);

		// slice out items
		if (!$this->pagination->getShowAll()) {
			$this->items = array_slice($this->items, $this->pagination->limitStart(), $items_per_page);
		}

		// set alphaindex
		if ($params->get('template.show_alpha_index')) {
			$this->alpha_index = $this->_getAlphaindex();
		}

	 	// set metadata
		$this->app->document->setTitle($this->app->zoo->buildPageTitle($this->tag));

		// create pathway
		$this->pathway->addItem(JText::_('Tags').': '.$this->tag, JRoute::_($this->app->route->tag($this->application->id, $this->tag)));

		// get template and params
		if (!$this->template = $this->application->getTemplate()) {
			return $this->app->error->raiseError(500, JText::_('No template selected'));
		}
		$this->params   = $params;

		// set renderer
		$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->template->getPath()));

		// display view
		$this->getView('tag')->addTemplatePath($this->template->getPath())->setLayout('tag')->display();
	}

	public function feed() {

		// get request vars
		$category_id = (int) $this->app->request->getInt('category_id', $this->params->get('category'));

		// get params
		$all_categories	= $this->application->getCategoryTree(true);

		// raise warning when category can not be accessed
		if (!isset($all_categories[$category_id])) {
			return $this->app->error->raiseWarning(404, JText::_('Unable to access category'));
		}

		$category 		= $all_categories[$category_id];
		$params 	 	= $category_id ? $category->getParams('site') : $this->application->getParams('frontpage');
		$show_feed_link = $params->get('config.show_feed_link', 0);
		$feed_title     = $params->get('config.feed_title', '');

		// raise error when feed is disabled
		if (empty($show_feed_link)) {
			return $this->app->error->raiseError(404, JText::_('Unable to access feed'));
		}

		// get feed items from category
		if ($category_id) {
			$categories = $category->getChildren(true);
		}
		$categories[$category->id] = $category;

		$feed_limit = $this->app->system->config->get('feed_limit');

		$feed_items = $this->app->table->item->getByCategory($this->application->id, array_keys($categories), true, null, array('_itempublish_up', '_reversed'), 0, $feed_limit, true);

		// set title
		if ($feed_title) {
			$this->app->system->document->setTitle($this->app->zoo->buildPageTitle(html_entity_decode($this->getView()->escape($feed_title))));
		}

		// set feed link
		$this->app->system->document->setLink(JRoute::_($category_id ? $this->app->route->category($category) : $this->app->route->frontpage($this->application->id)));

        // set feed description
        $this->app->system->document->setDescription(html_entity_decode($this->getView()->escape($this->app->system->document->getDescription())));

		// set renderer
		$renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->application->getTemplate()->getPath()));

		foreach ($feed_items as $feed_item) {

			// create feed item
			$item         	   = new JFeedItem();
			$item->title  	   = html_entity_decode($this->getView()->escape($feed_item->name));
			$item->link   	   = $this->app->route->item($feed_item);
			$item->date 	   = $feed_item->created;
			$item->author	   = $feed_item->getAuthor();
			$item->description = $this->_relToAbs($renderer->render('item.feed', array('item' => $feed_item)));

			// add to feed document
			$this->app->system->document->addItem($item);
		}

	}

	protected function _getAlphaindex() {
        return $this->app->application->getAlphaIndex($this->application);
	}

	protected function _relToAbs($text)	{

		// convert relative to absolute url
		$base = JURI::base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto)(?!\/)([^\"]*)\"/", "$1=\"$base\$2\"", $text);
		$base = JURI::getInstance()->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto)([^\"]*)\"/", "$1=\"$base\$2\"", $text);
		return $text;
	}

	// @deprecated as of 2.5.7
	protected function _buildPageTitle($title) {
		return $this->app->zoo->buildPageTitle($title);
	}

}

/*
	Class: DefaultControllerException
*/
class DefaultControllerException extends AppException {}
