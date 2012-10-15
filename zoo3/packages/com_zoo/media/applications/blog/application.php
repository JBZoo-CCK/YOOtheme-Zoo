<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class BlogApplication extends Application {

	/*
		Function: dispatch
			Dispatch application through executing the current controller.

		Returns:
			Void
	*/
	public function dispatch() {
		if ($template = $this->getTemplate()) {
			$this->app->path->register($template->getPath().'/classes', 'classes');
		}
		parent::dispatch();
	}

}