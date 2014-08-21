<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ApplicationTable
		The table class for application.
*/
class ApplicationTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, ZOO_TABLE_APPLICATION);
	}

	protected function _initObject($object) {

		// init vars
		$group  = $object->application_group;
		$class  = $group.'Application';

		// load application class
		if (!class_exists($class)) {
			if ($path = $this->app->path->path("applications:$group/application.php")) {
				require_once($path);
			}
		}

		if (class_exists($class)) {

			$data = get_object_vars($object);

			// create application instance
			$object = $this->app->object->create($class);

			// set data
			if (is_array($data)) {
				foreach ($data as $name => $value) {
					$object->$name = $value;
				}
			}

		}

		// workaround for php bug, which calls constructor before filling values
		if (is_string($object->params) || is_null($object->params)) {
			// decorate data as object
			$object->params = $this->app->parameter->create($object->params);
		}

		// add app
		$object->app = $this->app;

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'application:init'));

		return $object;
	}

	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		// Check ACL
		if (!$object->isAdmin()) {
			throw new ApplicationTableException('Invalid Access Permissions');
		}

		if ($object->name == '') {
			throw new ApplicationTableException('Invalid name');
		}

		if ($object->alias == '' || $object->alias != $this->app->string->sluggify($object->alias)) {
			throw new ApplicationTableException('Invalid slug');
		}

		$new = !(bool) $object->id;

		$result = parent::save($object);

		// trigger save event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'application:saved', compact('new')));

		return $result;

	}

	/*
		Function: delete
			Override. Delete object from database table.

		Returns:
			Boolean.
	*/
	public function delete($object) {

		// Check ACL
		if (!$object->isAdmin()) {
			throw new ApplicationTableException('Invalid Access Permissions');
		}

		// delete related categories
		$table = $this->app->table->category;
		$categories = $table->all(array('conditions' => array('application_id=?', $object->id)));
		foreach ($categories as $category) {
			$table->delete($category);
		}

		// delete related items
		$table = $this->app->table->item;
		$items = $table->all(array('conditions' => array('application_id=?', $object->id)));
		foreach ($items as $item) {
			$table->delete($item);
		}

		$result = parent::delete($object);

		// trigger deleted event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'application:deleted'));

		return $result;

	}

	/*
		Function: find
			Override. Find objects from database table.

		Returns:
			Boolean.
	*/
	public function find($mode = 'all', $options = null) {
		$result = parent::find($mode, $options);
		if ($this->app->system->application->isAdmin()) {
			if (!$result) return false;

			if (is_array($result)) {
				foreach ($result as $name => $application) {
					if (!$application->canManage()) {
						unset($result[$name]);
					}
				}
			} else {
				if (!$result->canManage()) {
					return false;
				}
			}
		}
		return $result;
	}
}

/*
	Class: ApplicationTableException
*/
class ApplicationTableException extends AppTableException {}