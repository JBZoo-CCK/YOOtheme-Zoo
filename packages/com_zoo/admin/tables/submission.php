<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: SubmissionTable
		The table class for submissions.
*/
class SubmissionTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, ZOO_TABLE_SUBMISSION);
	}

	protected function _initObject($object) {

		parent::_initObject($object);

		// workaround for php bug, which calls constructor before filling values
		if (is_string($object->params) || is_null($object->params)) {
			// decorate data as object
			$object->params = $this->app->parameter->create($object->params);
		}

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'submission:init'));

		return $object;
	}

	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		if ($object->name == '') {
			throw new SubmissionTableException('Invalid name');
		}

		if ($object->alias == '' || $object->alias != $this->app->string->sluggify($object->alias)) {
			throw new SubmissionTableException('Invalid slug');
		}

		if ($this->app->alias->submission->checkAliasExists($object->alias, $object->id)) {
			throw new SubmissionTableException('Slug already exists, please choose a unique slug');
		}

		// sanatize trusted mode
		if ($object->access == false) {
			$object->getParams()->set('trusted_mode', false);
		}

		return parent::save($object);
	}

}

/*
	Class: SubmissionTableException
*/
class SubmissionTableException extends AppException {}