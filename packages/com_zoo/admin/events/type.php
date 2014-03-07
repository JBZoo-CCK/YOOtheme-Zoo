<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Deals with element events.
 *
 * @package Component.Events
 */
class TypeEvent {

	/**
	 * Cleans and saves positions for templates, modules, plugins and submission layouts
	 *
	 * @param  AppEvent $event The event triggered
	 */
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

		// Fix option values
		foreach ($type->getElements() as $element) {
			if ($element instanceof ElementOption) {
				$options = $element->getConfig()->get('option', array());

				foreach ($options as &$option) {
					if (!strlen(trim($option['value']))) {
						$option['value'] = $app->string->sluggify($option['name']);
					}
					$option['value'] = $app->string->sluggify($option['value']);
				}
				$elements = $type->elements;
				$elements[$element->identifier]['option'] = $options;
				$type->elements = $elements;
			}
		}
		$type->clearElements();

	}

	/**
	 * Placeholder for the aftersave event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function aftersave($event) {}

	/**
	 * Copies the positions configuration upon type copy for templates, modules and plugins
	 *
	 * @param  AppEvent $event The event triggered
	 */
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

	/**
	 * Sanitize layout positions upon delete for templates, modules, plugins and submissions
	 *
	 * @param  AppEvent $event The event triggered
	 */
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
				$submission->getParams()->remove('form.'.$type->identifier);
				$table->save($submission);
			}
		}

	}

	/**
	 * Placeholder for the editDisplay event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function editDisplay($event) {

		$type = $event->getSubject();
		$html = $event['html'];

	}

}
