<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The helper class for object trees
 *
 * @package Component.Helpers
 * @since 2.0
 */
class TreeHelper extends AppHelper {

	/**
	 * Build tree.
	 *
	 * @param array $objects Objects
	 * @param string $classname the objects classname
	 * @param int $max_depth
	 * @param string $parent_property
	 *
	 * @return array Object list
	 * @since 2.0
	 */
	public function build($objects, $classname, $max_depth = 0, $parent_property = 'parent') {

		// create root category
		$root = $this->app->object->create($classname);
		$root->id = 0;
		$root->name = 'ROOT';
		$root->alias = '_root';
		$objects[0] = $root;

		foreach ($objects as $object) {
			// set parent and child relations
			if (isset($object->$parent_property, $objects[$object->$parent_property])) {
				$object->setParent($objects[$object->$parent_property]);
				$objects[$object->$parent_property]->addChild($object);
			}
		}

		if ($max_depth) {
			foreach ($objects as $object) {
				if (count($object->getPathway()) > $max_depth) {
					$object->getParent()->removeChild($object);
					$object->setParent($objects[0]);
					$objects[0]->addChild($object);
				}
			}
		}

		return $objects;
	}

	/**
	 * Build tree list which reflects the tree structure.
	 *
	 * @param string|int $id Object id to start
	 * @param array $objects Objects collection
	 * @param array $list Tree list return value
	 * @param string $prefix Sublevel prefix
	 * @param string $spacer Spacer
	 * @param string $indent Indent
	 * @param int $level Start level
	 * @param int $maxlevel Maximum level depth
	 *
	 * @return array Tree list
	 * @since 2.0
	 */
	public function buildList($id, $objects, $list = array(), $prefix = '<sup>|_</sup>&nbsp;', $spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $indent = '', $level = 0, $maxlevel = 9999) {

		if (isset($objects[$id]) && $level <= $maxlevel) {
			foreach ($objects[$id]->getChildren() as $child) {

				// set treename
				$id = $child->id;
				$list[$id] = $child;
				$list[$id]->treename = $indent.($indent == '' ? $child->name : $prefix.$child->name);
				$list = $this->buildList($id, $objects, $list, $prefix, $spacer, $indent.$spacer, $level + 1, $maxlevel);
			}
		}

		return $list;
	}

}