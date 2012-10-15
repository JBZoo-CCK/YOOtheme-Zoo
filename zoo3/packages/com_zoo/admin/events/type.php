<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: TypeEvent
		Type events.
*/
class TypeEvent {

	public static function beforesave($event) {

		$type = $event->getSubject();

		$app = $type->app;
		$application = $type->getApplication();

		// clean and save layout positions
		if (!empty($type->id) && $type->id != $type->identifier) {

			// update templates
			foreach ($app->path->dirs($application->getResource().'templates/') as $template) {
				$app->type->sanatizePositionsConfig($app->path->path($application->getResource().'templates/'.$template), $type);
			}

			// update modules
			foreach ($app->path->dirs('modules:') as $module) {
				if ($app->path->path("modules:$module/renderer")) {
					$app->type->sanatizePositionsConfig($app->path->path("modules:$module"), $type);
				}
			}

			// update plugins
			foreach ($app->path->dirs('plugins:') as $plugin_type) {
				foreach ($app->path->dirs('plugins:'.$plugin_type) as $plugin) {
					if ($app->path->path("plugins:$plugin_type/$plugin/renderer")) {
						$app->type->sanatizePositionsConfig($app->path->path("plugins:$plugin_type/$plugin"), $type);
					}
				}
			}

			// update submissions
			$table = $app->table->submission;
			$applications = array_keys($app->application->getApplications($application->getGroup()));
			if (!empty($applications)) {
				$submissions = $table->all(array('conditions' => 'application_id IN ('.implode(',', $applications).')'));
				foreach ($submissions as $submission) {
					$params = $submission->getParams();
					if ($tmp = $params->get('form.'.$type->id)) {
						$params->set('form.'.$type->identifier, $tmp)
							->remove('form.'.$type->id);
						$table->save($submission);
					}
				}
			}

		}

	}

	public static function aftersave($event) {}

	public static function copied($event) {

		$type = $event->getSubject();

		$app = $type->app;
		$application = $type->getApplication();
		$old_id = $event['old_id'];

		// copy template positions
		foreach ($app->path->dirs($application->getResource().'templates/') as $template) {
			$app->type->copyPositionsConfig($old_id, $app->path->path($application->getResource().'templates/'.$template), $type);
		}

		// copy module positions
		foreach ($app->path->dirs('modules:') as $module) {
			if ($app->path->path("modules:$module/renderer")) {
				$app->type->copyPositionsConfig($old_id, $app->path->path("modules:$module"), $type);
			}
		}

		// copy plugin positions
		foreach ($app->path->dirs('plugins:') as $plugin_type) {
			foreach ($app->path->dirs('plugins:'.$plugin_type) as $plugin) {
				if ($app->path->path("plugins:$plugin_type/$plugin/renderer")) {
					$app->type->copyPositionsConfig($old_id, $app->path->path("plugins:$plugin_type/$plugin"), $type);
				}
			}
		}

	}

	public static function deleted($event) {

		$type = $event->getSubject();

		$app = $type->app;
		$application = $type->getApplication();

		// update templates
		foreach ($app->path->dirs($application->getResource().'templates/') as $template) {
			$app->type->sanatizePositionsConfig($app->path->path($application->getResource().'templates/'.$template), $type, true);
		}

		// update modules
		foreach ($app->path->dirs('modules:') as $module) {
			if ($app->path->path("modules:$module/renderer")) {
				$app->type->sanatizePositionsConfig($app->path->path("modules:$module"), $type, true);
			}
		}

		// update plugins
		foreach ($app->path->dirs('plugins:') as $plugin_type) {
			foreach ($app->path->dirs('plugins:'.$plugin_type) as $plugin) {
				if ($app->path->path("plugins:$plugin_type/$plugin/renderer")) {
					$app->type->sanatizePositionsConfig($app->path->path("plugins:$plugin_type/$plugin"), $type, true);
				}
			}
		}

		// update submissions
		$table = $app->table->submission;
		$applications = array_keys($app->application->getApplications($application->getGroup()));
		if (!empty($applications)) {
			$submissions = $table->all(array('conditions' => 'application_id IN ('.implode(',', $applications).')'));
			foreach ($submissions as $submission) {
				$submission->getParams()
					->remove('form.'.$type->identifier);
				$table->save($submission);
			}
		}

	}

	public static function editDisplay($event) {

		$type = $event->getSubject();
		$html = $event['html'];

	}

}
