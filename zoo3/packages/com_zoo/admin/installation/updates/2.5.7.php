<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class Update257 implements iUpdate {

    /*
		Function: getNotifications
			Get preupdate notifications.

		Returns:
			Array - messages
	*/
	public function getNotifications($app) {

		$msg = array();
		$msg[] = JText::_('This update will require you to <a href="http://yootheme.com/zoo/documentation/advanced/assign-elements-to-layout-positions">assign some new elements to your submission layouts.</a>');
		return $msg;

	}

    /*
		Function: run
			Performs the update.

		Returns:
			bool - true if updated successful
	*/
	public function run($app) {

		foreach ($app->application->groups() as $application) {

			foreach ($application->getTypes() as $type) {

				foreach ($application->getTemplates() as $template) {

					$renderer = $app->renderer->create('item')->addPath($template->getPath());

					$path   = 'item';
					$prefix = 'item.';
					if ($renderer->pathExists($path.DIRECTORY_SEPARATOR.$type->id)) {
						$path   .= DIRECTORY_SEPARATOR.$type->id;
						$prefix .= $type->id.'.';
					}

					foreach ($renderer->getLayouts($path) as $layout) {

						// get layout metadata
						$metadata = $renderer->getLayoutMetaData($prefix.$layout);

						if ($metadata->get('type') == 'submission') {
							$config = $renderer->getConfig('item');
							$config = $app->data->create($config);
							if (isset($config[$application->getGroup().'.'.$type->id.'.'.$layout], $config[$application->getGroup().'.'.$type->id.'.'.$layout]['content'])) {
								if (@$config[$application->getGroup().'.'.$type->id.'.'.$layout]['content']['0']['element'] != '_itemname') {
									array_unshift($config[$application->getGroup().'.'.$type->id.'.'.$layout]['content'], array('altlabel' => '', 'required' => '1', 'element' => '_itemname'));
								}
							}
							$renderer->saveConfig($config, $template->getPath().'/renderer/item/positions.config');
						}
					}

					// try to fix submission.php files
					$regex = '/'.preg_quote('$this->renderer->render($this->layout_path, array(\'form\' => $this->form))').'/';
					$regex2 = '/'.preg_quote('$form->getItem()').'/';
					$regex3 = '/'.preg_quote('$form->').'[a-z]+\(.*?\)/i';
					foreach ($app->path->files($template->resource, true, '/submission\.php/') as $file) {
						try {

							$file = $app->path->path($template->resource.'/'.$file);
							if (is_writable($file)) {
								$output = file_get_contents($file);
								if (preg_match($regex, $output) || preg_match($regex2, $output)) {
									$output = preg_replace($regex, 'null', $output);
									$output = preg_replace($regex2, '$item', $output);
									$output = preg_replace($regex3, 'null', $output);
									JFile::write($file, $output);
								}
							}

						} catch (Exception $e) {}
					}
				}
			}
		}
	}
}