<?php
/**
* @package   ZOO Tag
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: TagModuleHelper
		The tag module helper class
*/
class TagModuleHelper extends AppHelper {

	const MIN_FONT_WEIGHT = 1;
	const MAX_FONT_WEIGHT = 10;

	public function buildTagCloud($application, $params) {

		// Get additional filters
		$mode = $params->get('mode', 'all');
		$category = false;
		$type = false;

		if ($mode == 'categories') {
			$category = $params->get('category', false);
			if ($params->get('subcategories')) {
				$categories = $application->getCategoryTree(true);
				if (isset($categories[$category])) {
					$category = array_merge(array($category), array_keys($categories[$category]->getChildren(true)));
				}
			}

			$type = false;
		} elseif ($mode == 'types') {
			$category = false;
			$type = $params->get('type', false);
		}

		// get tags
		$tags = $this->app->table->tag->getAll($application->id, null, null, 'items DESC', null, $params->get('count', 10), true, $category, $type);

		if (count($tags)) {

			// init vars
			$min_count 		 = $tags[count($tags)-1]->items;
			$max_count 		 = $tags[0]->items;
			$font_span 		 = ($max_count - $min_count) / 100;
			$font_class_span = (self::MAX_FONT_WEIGHT - self::MIN_FONT_WEIGHT) / 100;
			$menu_item 		 = $params->get('menu_item', 0);
			$itemid    		 = $menu_item ? '&Itemid='.$menu_item : '';

			// attach font size, href
			foreach ($tags as $tag) {
				$tag->weight = $font_span ? round(self::MIN_FONT_WEIGHT + (($tag->items - $min_count) / $font_span) * $font_class_span) : 1;
				$tag->href   = $this->app->route->tag($application->id, $tag->name, (int) $menu_item);
			}

			$this->orderTags($tags, $params->get('order'));

		}

		return $tags;

	}

	public function orderTags(&$tags, $order) {
		switch ($order) {
			case 'alpha':
				usort($tags, create_function('$a, $b', 'return strcmp($a->name, $b->name);'));
				break;
			case 'ralpha':
				usort($tags, create_function('$a, $b', 'return strcmp($b->name, $a->name);'));
				break;
			case 'acount':
				krsort($tags);
				$tags = array_merge($tags);
				break;
			case 'ocount':
				$this->_count_sort($tags);
				break;
			case 'icount':
				krsort($tags);
				$this->_count_sort($tags);
				break;
			case 'random':
				shuffle($tags);
				break;
		}
	}

	protected function _count_sort(&$tags) {
		$tags = array_merge($tags);
		$sorted_tags = array();
		$count = count($tags);
		$prefix = 1;
		$add = $count & 1 ? 1 : 0;
		for ($i = 0; $i < $count; $i++) {
			$sorted_tags[(int) (($count + $add + ($prefix * $i)) / 2)] = $tags[$i];
			$prefix *= -1;
		}
		ksort($sorted_tags);
		$tags = $sorted_tags;
	}

}