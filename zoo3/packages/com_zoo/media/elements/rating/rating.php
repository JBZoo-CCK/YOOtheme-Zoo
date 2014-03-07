<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ElementRating
		The rating element class
*/
class ElementRating extends Element {

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();

		// set callbacks
		$this->registerCallback('vote');

		if ($this->app->system->application->isAdmin()) {
			$this->registerCallback('reset');
		}
	}

	/*
		Function: getSearchData
			Get elements search data.

		Returns:
			String - Search data
	*/
	public function getSearchData() {
		return $this->getRating();
	}

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		return true;
	}

	/*
		Function: render
			Override. Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// init vars
		$params		= $this->app->data->create($params);
		$stars      = $this->config->get('stars');
		$allow_vote = $this->config->get('allow_vote');

		$disabled     	= $params->get('rating_disabled', false);
		$show_message 	= $params->get('show_message', false);
		$show_microdata = $params->get('show_microdata', false);

		// init vars
		$link = $this->app->link(array('task' => 'callelement', 'format' => 'raw', 'item_id' => $this->_item->id, 'element' => $this->identifier), false);

		$rating = $this->getRating();
		$votes = (int) $this->get('votes', 0);

		// render layout
		if ($layout = $this->getLayout()) {
			return $this->renderLayout($layout, compact('instance', 'stars', 'allow_vote', 'disabled', 'show_message', 'show_microdata', 'rating', 'votes', 'link'));
		}

		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		$controller = $this->app->request->getWord('controller');
		$url = $this->app->link(array('controller' => $controller, 'format' => 'raw', 'type' => $this->getType()->identifier, 'elm_id' => $this->identifier, 'item_id' => $this->getItem()->id), false);

		// render layout
		if ($layout = $this->getLayout('edit.php')) {
			return $this->renderLayout($layout, compact('url'));
		}

	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		$this->app->document->addScript('elements:rating/assets/js/rating.js');
		return $this;
	}

	public function reset() {

		$query = 'DELETE'
				.' FROM ' . ZOO_TABLE_RATING
				.' WHERE item_id = '.(int) $this->getItem()->id;

		$this->app->database->query($query);

		$this->set('votes', 0);
		$this->set('value', 0);

		//save item
		$this->app->table->item->save($this->getItem());

		return $this->edit();
	}

	/*
		Function: rating
			Get rating.

		Returns:
			String - Rating number
	*/
	public function getRating() {
		return number_format((double) $this->get('value', 0), 1);
	}

	/*
		Function: vote
			Execute vote.

		Returns:
			String - Message
	*/
	public function vote($vote = null) {

		// init vars
		$max_stars  = $this->config->get('stars');
		$allow_vote = $this->config->get('allow_vote', $this->app->joomla->getDefaultAccess());

		$db   = $this->app->database;
		$user = $this->app->user->get();
		$date = $this->app->date->create();
		$vote = (int) $vote;

		for ($i = 1; $i <= $max_stars; $i++) {
			$stars[] = $i;
		}

		if (!$this->app->user->canAccess($user, $allow_vote)) {
			return json_encode(array(
				'value' => 0,
				'message' => JText::_('NOT_ALLOWED_TO_VOTE')
			));
		}

		if (in_array($vote, $stars) && ($ip = $this->app->useragent->ip())) {

			// check if ip already exists
			$query = 'SELECT *'
				    .' FROM ' . ZOO_TABLE_RATING
			   	    .' WHERE element_id = '.$db->Quote($this->identifier)
			   	    .' AND item_id = '.(int) $this->_item->id
			   	    .' AND ip = '.$db->Quote($ip);

			$db->query($query);

			// voted already
			if ($db->getNumRows()) {
				return json_encode(array(
					'value' => 0,
					'message' => JText::_("You've already voted")
				));
			}

			// insert vote
			$query    = "INSERT INTO " . ZOO_TABLE_RATING
	   	               ." SET element_id = ".$db->Quote($this->identifier)
			   	       ." ,item_id = ".(int) $this->_item->id
		   	           ." ,user_id = ".(int) $user->id
		   	           ." ,value = ".(int) $vote
	   	               ." ,ip = ".$db->Quote($ip)
   	                   ." ,created = ".$db->Quote($date->toSQL());

			// execute query
			$db->query($query);

			// calculate rating/votes
			$query = 'SELECT AVG(value) AS rating, COUNT(id) AS votes'
				    .' FROM ' . ZOO_TABLE_RATING
				   	.' WHERE element_id = '.$db->Quote($this->identifier)
				    .' AND item_id = '.$this->_item->id
				    .' GROUP BY item_id';

			if ($res = $db->queryAssoc($query)) {
				$this->set('votes', $res['votes']);
				$this->set('value', $res['rating']);
			} else {
				$this->set('votes', 0);
				$this->set('value', 0);
			}
		}

		//save item
		$this->app->table->item->save($this->getItem());

		return json_encode(array(
			'value' => intval($this->getRating() / $max_stars * 100),
			'message' => sprintf(JText::_('%s rating from %s votes'), $this->getRating(), $this->get('votes'))
		));
	}

	/*
		Function: bindData
			Set data through data array.

		Parameters:
			$data - array

		Returns:
			Void
	*/
	public function bindData($data = array()) {
		parent::bindData($data);

		// calculate rating/votes
		$query = 'SELECT AVG(value) AS rating, COUNT(id) AS votes'
				.' FROM ' . ZOO_TABLE_RATING
				.' WHERE element_id = '.$this->app->database->Quote($this->identifier)
				.' AND item_id = '.$this->_item->id
				.' GROUP BY item_id';

		if ($this->_item->id && $res = $this->app->database->queryAssoc($query)) {
			$this->set('votes', $res['votes']);
			$this->set('value', $res['rating']);
		} else {
			$this->set('votes', 0);
			$this->set('value', 0);
		}

	}

}