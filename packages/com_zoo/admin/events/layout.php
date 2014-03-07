<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Deals with item events.
 *
 * @package Component.Events
 */
class LayoutEvent {

	/**
	 * Add extra layouts from modules and plugins
	 *
	 * @param  AppEvent $event The event object
	 */
	public static function init($event) {

		$app = $event->getSubject();

		$extensions = $event->getReturnValue();

		// get modules
		foreach ($app->path->dirs('modules:') as $module) {
			if ($app->path->path("modules:$module/renderer")) {
				$name = ($xml = simplexml_load_file($app->path->path("modules:$module/$module.xml"))) && $xml->getName() == 'extension' ? (string) $xml->name : $module;
				$extensions[$name] = array('type' => 'modules', 'name' => $name, 'path' => $app->path->path("modules:$module"));
			}
		}

		// get plugins
		foreach ($app->path->dirs('plugins:') as $plugin_type) {
			foreach ($app->path->dirs('plugins:'.$plugin_type) as $plugin) {
				if ($app->path->path("plugins:$plugin_type/$plugin/renderer")) {
					$resource = "plugins:$plugin_type/$plugin/$plugin.xml";
					$name = ($xml = simplexml_load_file($app->path->path($resource))) && $xml->getName() == 'extension' ? (string) $xml->name : $plugin;
					$name = rtrim($name, ' - ZOO');
					$extensions[$name] = array('type' => 'plugin', 'name' => $name, 'path' => $app->path->path("plugins:$plugin_type/$plugin"));
				}
			}
		}

		$event->setReturnValue($extensions);

	}

}