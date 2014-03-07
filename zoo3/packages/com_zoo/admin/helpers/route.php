<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The helper class for building links
 *
 * @package Component.Helpers
 * @since 2.0
 */
class RouteHelper extends AppHelper {

	/**
	 * The parsed menu items
	 * @var array
	 */
	protected $_menu_items;

	/**
	 * The route cache
	 * @var AppCache
	 */
	protected $_cache;

	/**
	 * The current category id on category view
	 * @var string
	 */
	protected $_category_id;

	/**
	 * The active menu item id
	 * @var string
	 */
	protected $_active_menu_item_id;

	/**
	 * Class constructor
	 *
	 * @param string $app App instance.
	 * @since 2.0
	 */
	public function __construct($app) {
		parent::__construct($app);

		if ($app->get('cache_routes', false)) {
			// get route cache
			// refreshes after one hour automatically
			$this->_cache = $app->cache->create($app->path->path('cache:') . '/routes', true, 3600, 'apc');
			if (!$this->_cache || !$this->_cache->check()) {
				$this->_cache = null;
			} else {
				$this->_find(null, null);
				$key = json_encode($this->_menu_items);
				if (!$this->_cache->get($key)) {
					$this->_cache->clear()->set($key, true)->save();
				}
			}
		}

		if ($app->request->getCmd('task') == 'category' || $app->request->getCmd('view') == 'category') {
			$this->_category_id = (int) $app->request->getInt('category_id', (method_exists($app->system->application, 'getParams') ? $app->system->application->getParams()->get('category') : null));
		}

		if ($menu_item = $app->menu->getActive()) {
			$this->_active_menu_item_id = $menu_item->id;
		}

	}

	/**
	 * Gets this route helpers link base
	 *
	 * @return string the link base
	 * @since 2.0
	 */
	public function getLinkBase() {
		return 'index.php?option='.$this->app->component->self->name;
	}

	/**
	 * Gets route to alphaindex
	 *
	 * @param int $application_id
	 * @param string $alpha_char
	 *
	 * @return string the route
	 * @since 2.0
	 */
	public function alphaindex($application_id, $alpha_char = null) {

		$key = $this->_active_menu_item_id.'-alphaindex-'.$application_id.'_'.$alpha_char;
		if ($this->_cache && $link = $this->_cache->get($key)) {
			return $link;
		}

		// build frontpage link
		$link = $this->getLinkBase().'&task=alphaindex&app_id='.$application_id;
		$link .= $alpha_char !== null ? '&alpha_char='.$alpha_char : '';

		if ($menu_item = $this->_find('frontpage', $application_id) or $menu_item = $this->app->menu->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		// store link for future lookups
		if ($this->_cache) {
			$this->_cache->set($key, $link)->save();
		}

		return $link;
	}

	/**
	 * Gets route to category
	 *
	 * @param Category $category
	 * @param boolean $route If it should be run through JRoute::_()
     * @param int $force_id
	 *
	 * @return string the route
	 * @since 2.0
	 */
	public function category($category, $route = true, $force_id = 0) {

		$key = $this->_active_menu_item_id.'-category-'.$category->application_id.'_'.$category->id.'_'.$route.'_'.$force_id;
		if ($this->_cache && $link = $this->_cache->get($key)) {
			return $link;
		}

        if (!$force_id && $this->app->request->getBool('f') && $this->app->request->getString('category_id') == $category->id) {
            $force_id = $this->app->request->getInt('Itemid');
        }

        $itemid = null;
		$this->app->table->application->get($category->application_id)->getCategoryTree(true);

		// Priority 1: direct link to category
		if ($menu_item = $this->_find('category', $category->id)) {
			$link = $menu_item->link;
            $itemid = $menu_item->id;
		} else {

			// build category link
			$link = $this->getLinkBase().'&task=category&category_id='.$category->id;

			// Priority 2: find in category path
			if ($menu_item = $this->_findInCategoryPath($category)) {
                $itemid = $menu_item->id;
			} else {
				// Priority 3: link to frontpage || Priority 4: current item id
				if ($menu_item = $this->_find('frontpage', $category->application_id) or $menu_item = $this->app->menu->getActive()) {
                    $itemid = $menu_item->id;
				}
			}
		}

        if ($force_id && $force_id != $itemid) {
            $itemid = $force_id;
            $link .= '&f=1&task=category&category_id='.$category->id;
        }

        if ($itemid) {
            $link .= '&Itemid='.$itemid;
        }

		if ($route) {
			$link = JRoute::_($link);
		}

		// store link for future lookups
		if ($this->_cache) {
			$this->_cache->set($key, $link)->save();
		}
		return $link;


	}

	/**
	 * Get route to comment
	 *
	 * @param Comment $comment
	 * @param boolean $route If it should be run through JRoute::_()
	 *
	 * @return string the route
	 * @since 2.0
	 */
	public function comment($comment, $route = true) {
		return $this->item($comment->getItem(), $route).'#comment-'.$comment->id;
	}

	/**
	 * Get route to feed
	 *
	 * @param  Category $category  The category to show the feeds for
	 * @param  string $feed_type   The type of the feed
	 *
	 * @return string            The route
	 *
	 * @since 2.0
	 */
	public function feed($category, $feed_type) {

		$key = $this->_active_menu_item_id.'-feed-'.$category->id.'_'.$feed_type;
		if ($this->_cache && $link = $this->_cache->get($key)) {
			return $link;
		}

		// build feed link
		$link = $this->getLinkBase().'&task=feed&app_id='.$category->application_id.'&category_id='.$category->id.'&format=feed&type='.$feed_type;

		if ($menu_item = $this->_find('frontpage', $category->application_id) or $menu_item = $this->app->menu->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		// store link for future lookups
		if ($this->_cache) {
			$this->_cache->set($key, $link)->save();
		}

		return $link;
	}

	/**
	 * Gets route to frontpage
	 *
	 * @param int $application_id
	 *
	 * @return string the route
	 * @since 2.0
	 */
	public function frontpage($application_id) {

		$key = $this->_active_menu_item_id.'-frontpage-'.$application_id;
		if ($this->_cache && $link = $this->_cache->get($key)) {
			return $link;
		}

		// Priority 1: direct link to frontpage
		if ($menu_item = $this->_find('frontpage', $application_id)) {
			return $menu_item->link.'&Itemid='.$menu_item->id;
		}

		// build frontpage link
		$link = $this->getLinkBase().'&task=frontpage';

		// Priority 2: current item id
		if ($menu_item = $this->app->menu->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		// store link for future lookups
		if ($this->_cache) {
			$this->_cache->set($key, $link)->save();
		}

		return $link;
	}

	/**
	 * Get route to item
	 *
	 * @param Item $item
	 * @param boolean $route If it should be run through JRoute::_()
	 *
	 * @return string the route
	 * @since 2.0
	 */
	public function item($item, $route = true) {

		$category_id = $this->_category_id;

		$key = $this->_active_menu_item_id.'-item-'.$item->application_id.'_'.$category_id.'_'.$item->id.'_'.$route;
		if ($this->_cache && $cached = $this->_cache->get($key)) {
			return $cached;
		}

		// Priority 1: direct link to item
		if ($menu_item = $this->_find('item', $item->id)) {

			$link = $menu_item->link.'&Itemid='.$menu_item->id;

		} else {

			$itemid = null;

			// build item link
			$link = $this->getLinkBase().'&task=item&item_id='.$item->id;

			// are we in category view?
			$this->app->table->application->get($item->application_id)->getCategoryTree(true);
			$categories = null;
			if ($category_id) {
				$categories  = array_filter($item->getRelatedCategoryIds(true));
				$category_id = in_array($category_id, $categories) ? $category_id : null;
			}

			if (!$category_id) {

				$primary_id = $item->getPrimaryCategoryId();

				// Priority 2: direct link to primary category
				if ($primary_id && $menu_item = $this->_find('category', $primary_id)) {
					$itemid = $menu_item->id;
					// Priority 3: find in primary category path
				} else if ($primary_id and $primary = $item->getPrimaryCategory() and $menu_item = $this->_findInCategoryPath($primary)) {
					$itemid = $menu_item->id;
				} else {
					$categories = is_null($categories) ? array_filter($item->getRelatedCategoryIds(true)) : $categories;
					foreach ($categories as $category) {
						// Priority 4: direct link to any related category
						if ($menu_item = $this->_find('category', $category)) {
							$itemid = $menu_item->id;
							break;
						}
					}

					if (!$itemid) {
						$categories = $item->getRelatedCategories(true);
						foreach ($categories as $category) {
							// Priority 5: find in any related categorys path
							if ($menu_item = $this->_findInCategoryPath($category)) {
								$itemid = $menu_item->id;
								break;
							}
						}
					}

					// Priority 6: link to frontpage
					if (!$itemid && $menu_item = $this->_find('frontpage', $item->application_id)) {
						$itemid = $menu_item->id;
					}
				}
			} elseif ($category_id != $item->getPrimaryCategoryId()) {
				$link .= '&category_id='.$category_id;
			}

			if ($itemid) {
				$link .= '&Itemid='.$itemid;
				// Priority 7: current item id
			} else if ($menu_item = $this->app->menu->getActive()) {
				$link .= '&Itemid='.$menu_item->id;
			}
		}

		if ($route) {
			$link = JRoute::_($link);
		}

		// store link for future lookups
		if ($key && $this->_cache) {
			$this->_cache->set($key, $link)->save();
		}

		return $link;

	}

	/**
	 * Get route to mysubmissions view
	 *
	 * @param Submission $submission
	 *
	 * @return string the route
	 * @since 2.0
	 */
	public function mysubmissions($submission) {

		$link = $this->getLinkBase().'&view=submission&layout=mysubmissions&submission_id='.$submission->id;

		if ($menu_item = $this->app->menu->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;
	}

	/**
	 * Get route to submission view
	 *
	 * @param Submission $submission
	 * @param string $type_id
	 * @param string $hash
	 * @param int $item_id
	 * @param string $redirect
	 *
	 * @return string the route
	 * @since 2.0
	 */
	public function submission($submission, $type_id, $hash = null, $item_id = 0, $redirect = null) {

		$hash = empty($hash) ? $this->app->submission->getSubmissionHash($submission->id, $type_id, $item_id) : $hash;

		$redirect = !empty($redirect) ? '&redirect='.urlencode($redirect) : '';
		$item_id = !empty($item_id) ? '&item_id='.$item_id : '';

		$link = $this->getLinkBase().'&view=submission&layout=submission&submission_id='.$submission->id.'&type_id='.$type_id.$item_id.'&submission_hash='.$hash.$redirect;

		if ($menu_item = $this->app->menu->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;
	}

	/**
	 * Get route to tag view
	 *
	 * @param int $application_id
	 * @param string $tag
     * @param int $force_id
	 *
	 * @return string the route
	 * @since 2.0
	 */
	public function tag($application_id, $tag, $force_id = 0) {

		$key = $this->_active_menu_item_id.'-tag-'.$application_id.'_'.$tag.'_'.$force_id;
		if ($this->_cache && $link = $this->_cache->get($key)) {
			return $link;
		}

        if (!$force_id && $this->app->request->getBool('f') && $this->app->request->getString('tag') == $tag) {
            $force_id = $this->app->request->getInt('Itemid');
        }

		// build tag link
		$link = $this->getLinkBase().'&task=tag&tag='.$tag.'&app_id='.$application_id;

		// Priority 1: link to frontpage || Priority 2: current item id
        $item_id = '';
		if ($menu_item = $this->_find('frontpage', $application_id) or $menu_item = $this->app->menu->getActive()) {
            if ($force_id && $force_id != $menu_item->id) {
                $item_id = '&Itemid='.$force_id;
                $link .= '&f=1&task=tag&tag='.$tag.'&app_id='.$application_id;
            } else {
                $item_id = '&Itemid='.$menu_item->id;
            }
		}

        $link .= $item_id;

		// store link for future lookups
		if ($this->_cache) {
			$this->_cache->set($key, $link)->save();
		}

		return $link;
	}

	/**
	 * Finds the category in the pathway
	 *
	 * @param Category $category
	 * @return stdClass menu item
	 * @since 2.0
	 */
	protected function _findInCategoryPath($category) {
		foreach ($category->getPathway() as $id => $cat) {
			if ($menu_item = $this->_find('category', $id)) {
				return $menu_item;
			}
		}
	}

	/**
	 * Finds a menu item by its type and id in the menu items
	 *
	 * @param string $type
	 * @param string $id
	 *
	 * @return stdClass menu item
	 * @since 2.0
	 */
	protected function _find($type, $id) {
		if ($this->_menu_items == null) {
			$menu_items = $this->app->system->application->getMenu('site')->getItems('component_id', JComponentHelper::getComponent('com_zoo')->id);
			$menu_items = $menu_items ? $menu_items : array();

			$this->_menu_items = array_fill_keys(array('frontpage', 'category', 'item', 'submission', 'mysubmissions'), array());
			foreach ($menu_items as $menu_item) {
				switch (@$menu_item->query['view']) {
					case 'frontpage':
						$this->_menu_items['frontpage'][$this->app->parameter->create($menu_item->params)->get('application')] = $menu_item;
						break;
					case 'category':
						$this->_menu_items['category'][$this->app->parameter->create($menu_item->params)->get('category')] = $menu_item;
						break;
					case 'item':
						$this->_menu_items['item'][$this->app->parameter->create($menu_item->params)->get('item_id')] = $menu_item;
						break;
					case 'submission':
						$this->_menu_items[(@$menu_item->query['layout'] == 'submission' ? 'submission' : 'mysubmissions')][$this->app->parameter->create($menu_item->params)->get('submission')] = $menu_item;
						break;
				}
			}
		}

		return @$this->_menu_items[$type][$id];
	}

	public function clearCache() {
		if ($this->_cache) {
			$this->_cache->clear()->save();
		}
	}
}
