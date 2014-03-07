<?php
/**
* @package   Search - ZOO
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSearchZoosearch extends JPlugin {

	/* menu item mapping */
	public $menu;

	public $app;

	/*
		Function: plgSearchZoosearch
		  Constructor.

		Parameters:
	      $subject - Array
	      $params - Array

	   Returns:
	      Void
	*/
	public function plgSearchZoosearch($subject, $params) {

		// make sure ZOO exists
		if (!JComponentHelper::getComponent('com_zoo', true)->enabled) {
			return;
		}

		parent::__construct($subject, $params);

		// load config
		jimport('joomla.filesystem.file');
		if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php') || !JComponentHelper::getComponent('com_zoo', true)->enabled) {
			return;
		}
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		$this->app = App::getInstance('zoo');
	}

	/*
		Function: onSearchAreas
		  Get search areas.

	   Returns:
	      Array - Search areas
	*/
	public function onSearchAreas() {
		static $areas = array();
		return $areas;
	}

	/*
		Function: onSearch
		  Get search results. The sql must return the following fields that are used in a common display routine: href, title, section, created, text, browsernav

		Parameters:
	      $text - Target search string
	      $phrase - Matching option, exact|any|all
	      $ordering - Ordering option, newest|oldest|popular|alpha|category
	      $areas - An array if the search it to be restricted to areas, null if search all

	   Returns:
	      Array - Search results
	*/
	public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null) {
		$db	  = $this->app->database;

		// init vars
		$now  = $db->Quote($this->app->date->create()->toSQL());
		$null = $db->Quote($db->getNullDate());
		$text = trim($text);

		// return empty array, if no search text provided
		if (empty($text)) {
			return array();
		}

		// get plugin info
	 	$plugin   = JPluginHelper::getPlugin('search', 'zoosearch');
	 	$params   = $this->app->parameter->create($plugin->params);
		$fulltext = $params->get('search_fulltext', 0) && strlen($text) > 3 && intval($db->getVersion()) >= 4;
		$limit    = $params->get('search_limit', 50);

        $elements = array();
        foreach ($this->app->application->groups() as $application) {
            foreach($application->getTypes() as $type) {
                foreach ($type->getElements() as $element) {
                    if (!$element->canAccess()) {
                        $elements[] = $db->Quote($element->identifier);
                    }
                }
            }
        }

        $access = $elements ? 'NOT element_id in ('.implode(',', $elements).')' : '1';

		// prepare search query
		switch ($phrase) {
			case 'exact':

				if ($fulltext) {
					$text    = $db->escape($text);
					$where[] = "MATCH(a.name) AGAINST ('\"{$text}\"' IN BOOLEAN MODE)";
					$where[] = "MATCH(b.value) AGAINST ('\"{$text}\"' IN BOOLEAN MODE) AND $access";
					$where[] = "MATCH(c.name) AGAINST ('\"{$text}\"' IN BOOLEAN MODE)";
					$where   = implode(" OR ", $where);
				} else {
					$text	= $db->Quote('%'.$db->escape($text, true).'%', false);
					$like   = array();
					$like[] = 'a.name LIKE '.$text;
					$like[] = "b.value LIKE $text AND $access";
					$like[] = 'c.name LIKE '.$text;
					$where 	= '(' .implode(') OR (', $like).')';
				}

				break;

			case 'all':
			case 'any':
			default:

				if ($fulltext) {
					$text    = $db->escape($text);
					$where[] = "MATCH(a.name) AGAINST ('{$text}' IN BOOLEAN MODE)";
					$where[] = "MATCH(b.value) AGAINST ('{$text}' IN BOOLEAN MODE) AND $access";
					$where[] = "MATCH(c.name) AGAINST ('{$text}' IN BOOLEAN MODE)";
					$where   = implode(" OR ", $where);
				} else {
					$words 	= explode(' ', $text);
					$wheres = array();

					foreach ($words as $word) {
						$word     = $db->Quote('%'.$db->escape($word, true).'%', false);
						$like     = array();
						$like[]   = 'a.name LIKE '.$word;
						$like[]   = 'EXISTS (SELECT value FROM '.ZOO_TABLE_SEARCH.' WHERE a.id = item_id AND value LIKE '.$word.' AND '.$access.')';
						$like[]   = 'EXISTS (SELECT name FROM '.ZOO_TABLE_TAG.' WHERE a.id = item_id AND name LIKE '.$word.')';
						$wheres[] = implode(' OR ', $like);
					}

					$where = '('.implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres).')';
				}
		}

		// set search ordering
		switch ($ordering) {
			case 'newest':
				$order = 'a.created DESC';
				break;

			case 'oldest':
				$order = 'a.created ASC';
				break;

			case 'popular':
				$order = 'a.hits DESC';
				break;

			case 'alpha':
			case 'category':
			default:
				$order = 'a.name ASC';
		}

		// set query options
		$select     = "DISTINCT a.*";
        $from       = ZOO_TABLE_ITEM." AS a"
			         ." LEFT JOIN ".ZOO_TABLE_SEARCH." AS b ON a.id = b.item_id"
		             ." LEFT JOIN ".ZOO_TABLE_TAG." AS c ON a.id = c.item_id";
		$conditions = array("(".$where.")"
                     ." AND a.searchable = 1"
                     ." AND a." . $this->app->user->getDBAccessString()
                     ." AND (a.state = 1"
		             ." AND (a.publish_up = ".$null." OR a.publish_up <= ".$now.")"
		             ." AND (a.publish_down = ".$null." OR a.publish_down >= ".$now."))");

		// execute query
		$items = $this->app->table->item->all(compact('select', 'from', 'conditions', 'order', 'limit'));

		// create search result rows
		$rows = array();
		if (!empty($items)) {

			// set renderer
			$renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('plugins:search/zoosearch/')));

			foreach ($items as $item) {
				$row = new stdClass();
				$row->title = $item->name;
				$row->text = $renderer->render('item.default', array('item' => $item));
				$row->href = $this->app->route->item($item);
				$row->created = $item->created;
				$row->section = '';
				$row->browsernav = 2;
				$rows[] = $row;
			}
		}

		return $rows;
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