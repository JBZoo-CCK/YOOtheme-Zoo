<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Type helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class TypeHelper extends AppHelper {

	/**
	 * Sets a unique type identifier
	 *
	 * @param Type $type
	 *
	 * @return Type
	 * @since 2.0
	 */
	public function setUniqueIndentifier($type) {
		if ($type->id != $type->identifier) {
			// check identifier
			$tmp_identifier = $type->identifier;

			// build resource
			$resource = $type->getApplication()->getResource().'types/';

			$i = 2;
			while ($this->app->path->path($resource.$tmp_identifier.'.config')) {
				$tmp_identifier = $type->identifier.'-'.$i++;
			}
			$type->identifier = $tmp_identifier;
		}
		return $type;
	}

	/**
	 * Sanatize positions config file (after renaming or deleting a type)
	 *
	 * @param string $path Path to renderer
	 * @param Type $type The type to sanatize
	 * @param boolean $delete if set to true, type will be removed
	 *
	 * @since 2.0
	 */
	public function sanatizePositionsConfig($path, $type, $delete = false) {

		// get renderer
		$renderer = $this->app->renderer->create('item')->addPath($path);

		// get group
		$group = $type->getApplication()->getGroup();

		// rename folder if special type
		if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$type->id)) {
			$folder = $path.DIRECTORY_SEPARATOR.$renderer->getFolder().DIRECTORY_SEPARATOR.'item'.DIRECTORY_SEPARATOR;
			if ($delete) {
				JFolder::delete($folder.$type->id);
			} else {
				JFolder::move($folder.$type->id, $folder.$type->identifier);
			}
		}

		// get positions and config
		$config = $renderer->getConfig('item');
		$params = $config->get($group.'.'.$type->id.'.');
		if (!$delete) {
			$config->set($group.'.'.$type->identifier.'.', $params);
		}
		$config->remove($group.'.'.$type->id.'.');
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');
	}

	/**
	 * Copy positions config file
	 *
	 * @param string $id the old type id
	 * @param string $path Path to renderer
	 * @param Type $type The type to copy
	 *
	 * @since 2.0
	 */
	public function copyPositionsConfig($id, $path, $type) {

		// get renderer
		$renderer = $this->app->renderer->create('item')->addPath($path);

		// get group
		$group = $type->getApplication()->getGroup();
		$original = $type->getApplication()->getType($id);

		$element_mapping = array_combine(array_keys($type->elements), array_keys($original->elements));

		// rename folder if special type
		if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$id)) {
			$folder = $path.DIRECTORY_SEPARATOR.$renderer->getFolder().DIRECTORY_SEPARATOR.'item'.DIRECTORY_SEPARATOR;
			JFolder::copy($folder.$id, $folder.$type->id);
		}

		// get positions and config
		$config = $renderer->getConfig('item');
		$params = $config->get($group.'.'.$id.'.');

		// match new element ids to prev ids
		if ($element_mapping) {
			foreach ($params as $layout => $positions) {
				foreach ($positions as $position => $elements) {
					foreach ($elements as $i => $element_config) {
						foreach ($type->elements as $elem_id => $element) {
							if (isset($element_mapping[$elem_id]) && $element_config['element'] == $element_mapping[$elem_id]) {
								$params[$layout][$position][$i]['element'] = $elem_id;
							}
						}
					}
				}
			}
		}

		$config->set($group.'.'.$type->id.'.', $params);
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');
	}

	/**
	 * Returns layouts for a type of an app.
	 *
	 * @param Type $type
	 * @param string $layout_type
	 *
	 * @return array The layouts
	 * @since 2.0
	 */
	public function layouts($type, $layout_type = '') {

		$result = array();

		// get template
		if ($template = $type->getApplication()->getTemplate()) {

			// get renderer
			$renderer = $this->app->renderer->create('item')->addPath($template->getPath());

			$path = 'item';
			$prefix = 'item.';
			if ($renderer->pathExists($path.DIRECTORY_SEPARATOR.$type->id)) {
				$path .= DIRECTORY_SEPARATOR.$type->id;
				$prefix .= $type->id.'.';
			}

			foreach ($renderer->getLayouts($path) as $layout) {
				$metadata = $renderer->getLayoutMetaData($prefix.$layout);
				if (empty($layout_type) || ($metadata->get('type') == $layout_type)) {
					$result[$layout] = $metadata;
				}
			}
		}

		return $result;
	}

}