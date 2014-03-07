<?php
/**
* @package   ZOO Category
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CategoryModuleHelper
		The category module helper class
*/
class CategoryModuleHelper extends AppHelper {

    public function render($category, $params, $level = 0, $flat = false, $attribs = null, $expanded = false) {

		// init vars
		$menu_item = $params->get('menu_item');
		$max_depth = (int) $params->get('depth', 0);
		if (!$current_id = (int) $this->app->request->getInt('category_id', $this->app->system->application->getParams()->get('category'))) {
			if ($item = $this->app->table->item->get((int) $this->app->request->getInt('item_id', $this->app->system->application->getParams()->get('item_id', 0)))) {
				$current_id = $item->getPrimaryCategoryId();
			}
		}

		$result = array("<ul $attribs>");
		foreach ($category->getChildren($flat ? true : false) as $category) {

			$path = array_reverse($category->getPath());
			$depth = count(array_slice($path, array_search($params->get('category', 0), $path))) - 1;
			if ($max_depth && $max_depth < $depth) {
				continue;
			}

			$current = $current_id == $category->id;
			$active = $current || in_array($current_id, array_keys($category->getChildren(true)));
			$parent = $category->hasChildren() && !($max_depth && $max_depth < $depth + 1);
			$url = $this->app->route->category($category, true, $menu_item);
			$class = ' class="'.($flat ? '' : 'level'.$level).($parent ? ' parent' : '').($current ? ' current' : '').($active ? ' active' : '').'"';

			$result[] = "<li$class>";
			if ($params->get('add_count', 0)) {
				$result[] = "<a href=\"$url\"$class><span>{$category->name} ({$category->itemCount()})</span></a>";
			} else {
				$result[] = "<a href=\"$url\"$class><span>{$category->name}</span></a>";
			}
			if (!$flat && ($active || $expanded) && $parent) {
				$result[] = $this->render($category, $params, $level+1, $flat, 'class="level'.($level+1).'"', $expanded);
			}
			$result[] = '</li>';

		}
		$result[] = '</ul>';

		return implode("\n", $result);
	}

}