<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

function ZooBuildRoute(&$query) {

	$app = App::getInstance('zoo');

	// init vars
	$segments = array();

	// frontpage
	$task = 'frontpage';

		if (@$query['task'] == $task || @$query['view'] == $task) {
			if (@$query['task'] == $task) {
				$segments[] = $task;
			}
			unset($query['task'], $query['view'], $query['layout']);

			// pagination
			if (isset($query['page'])) {
				$segments[] = $query['page'];
				unset($query['page']);
			}
		}

	// category
	$task = 'category';

		if (@$query['task'] == $task || @$query['view'] == $task) {
			if (@$query['task'] == $task) {
				$segments[] = $task;
			}
			if (@$query['category_id']) {
				$segments[] = $app->alias->category->translateIDToAlias((int) $query['category_id']);
			}
			unset($query['task'], $query['view'], $query['layout'], $query['category_id']);

			// pagination
			if (isset($query['page'])) {
				$segments[] = $query['page'];
				unset($query['page']);
			}
		}

	// alpha index
	$task = 'alphaindex';

		if ((@$query['task'] == $task || @$query['view'] == $task) && isset($query['alpha_char'], $query['app_id'])) {
			$segments[] = $task;
            if (!($menu = $app->system->application->getMenu('site')
                    and $menu instanceof JMenu
                    and isset($query['Itemid'])
                    and $item = $menu->getItem($query['Itemid'])
                    and @$item->component == 'com_zoo'
                    and $app_id = $item->params->get('application')
                    and $query['app_id'] == $app_id)) {
                // Set app alias in the url only if we can't get the app_id from the menu item
                $segments[] = $app->alias->application->translateIDToAlias((int) $query['app_id']);
            }
			$segments[] = $query['alpha_char'];

			unset($query['task'], $query['view'], $query['alpha_char'], $query['app_id']);

			// pagination
			if (isset($query['page'])) {
				$segments[] = $query['page'];
				unset($query['page']);
			}
		}

	// tag
	$task = 'tag';

		if ((@$query['task'] == $task || @$query['view'] == $task) && isset($query['tag'], $query['app_id'])) {
			$segments[] = $task;
            if (!($menu = $app->system->application->getMenu('site')
                    and $menu instanceof JMenu
                    and isset($query['Itemid'])
                    and $item = $menu->getItem($query['Itemid'])
                    and @$item->component == 'com_zoo'
                    and $app_id = $item->params->get('application')
                    and $query['app_id'] == $app_id)) {
                // Set app alias in the url only if we can't get the app_id from the menu item
                $segments[] = $app->alias->application->translateIDToAlias((int) $query['app_id']);
            }
			$segments[] = $query['tag'];

			unset($query['task'], $query['view'], $query['tag'], $query['app_id']);

			// pagination
			if (isset($query['page'])) {
				$segments[] = $query['page'];
				unset($query['page']);
			}
		}

	// item
	$task = 'item';

		if (@$query['task'] == $task || @$query['view'] == $task) {
			if (@$query['task'] == $task && @$query['item_id']) {
				$segments[] = $task;
				$segments[] = $app->alias->item->translateIDToAlias((int) $query['item_id']);
			}
			unset($query['task'], $query['view'], $query['layout'], $query['item_id']);
		}

	// feed
	$task = 'feed';

		if ((@$query['task'] == $task || @$query['view'] == $task) && isset($query['type'], $query['app_id'], $query['category_id'])) {
			$segments[] = $task;
			$segments[] = $query['type'];
			$segments[] = $app->alias->application->translateIDToAlias((int) $query['app_id']);
			if ($query['category_id']) {
				$segments[] = $app->alias->category->translateIDToAlias((int) $query['category_id']);
			}
			unset($query['task'], $query['view'], $query['type'], $query['app_id'], $query['category_id']);
		}

	// submission
	$task = 'submission';

		if (((@$query['task'] == $task || @$query['view'] == $task) && @$query['layout'] == 'submission')) {
			$segments[] = $task;
			$segments[] = @$query['layout'];
			if (isset($query['submission_id'], $query['type_id'], $query['submission_hash'])) {
				$segments[] = $app->alias->submission->translateIDToAlias((int) $query['submission_id']);
				$segments[] = $query['type_id'];
				$segments[] = $query['submission_hash'];
			}
			if (isset($query['item_id'])) {
				$segments[] = $app->alias->item->translateIDToAlias((int) @$query['item_id']);
			}
			unset($query['task'], $query['view'], $query['layout'], $query['submission_id'], $query['type_id'], $query['submission_hash'], $query['item_id']);
		}

	// submission mysubmissions
	$task = 'submission';

		if (((@$query['task'] == $task || @$query['view'] == $task) && @$query['layout'] == 'mysubmissions')) {
			$segments[] = $task;
			$segments[] = @$query['layout'];
			$segments[] = $app->alias->submission->translateIDToAlias((int) @$query['submission_id']);
			unset($query['task'], $query['view'], $query['layout'], $query['submission_id']);
		}

	$app->event->dispatcher->notify($app->event->create(null, 'application:sefbuildroute', array('segments' => &$segments, 'query' => &$query)));

	return $segments;
}

function ZooParseRoute($segments) {

	$app = App::getInstance('zoo');

	// init vars
	$vars  = array();
	$count = count($segments);

	// fix segments (see JRouter::_decodeSegments)
	foreach (array_keys($segments) as $key) {
		$segments[$key] = str_replace(':', '-', $segments[$key]);
	}

	// frontpage (with optional pagination)
	$task = 'frontpage';

		if ($count == 1 && $segments[0] == $task) {
			$vars['task'] = $task;
		}

		if ($count == 2 && $segments[0] == $task) {
			$vars['task'] = $task;
			$vars['page'] = (int) $segments[1];
		}

	// category (with optional pagination)
	$task = 'category';

		if ($count == 2 && $segments[0] == $task) {
			$vars['task']        = $task;
			$vars['category_id'] = (int) $app->alias->category->translateAliasToID($segments[1]);
		}

		if ($count == 3 && $segments[0] == $task) {
			$vars['task']        = $task;
			$vars['category_id'] = (int) $app->alias->category->translateAliasToID($segments[1]);
			$vars['page']        = (int) $segments[2];
		}

	// alpha index (with optional pagination)
	$task = 'alphaindex';

        if ($count == 2 && $segments[0] == $task && $menu_item = $app->menu->getActive()) {
            $vars['task']       = $task;
            $vars['alpha_char'] = (string) $segments[1];
            $vars['app_id'] = $menu_item->params->get('application');
        }

        if ($count == 3 && $segments[0] == $task) {
            $vars['task']           = $task;
            if (!$vars['app_id'] = (int) $app->alias->application->translateAliasToID($segments[1]) and $menu_item = $app->menu->getActive()) {
                $vars['app_id']     = $menu_item->params->get('application');
                $vars['alpha_char'] = (string) $segments[1];
                $vars['page']       = (int) $segments[2];
            } else {
                $vars['alpha_char'] = (string) $segments[2];
            }
        }

        if ($count == 4 && $segments[0] == $task) {
            $vars['task']       = $task;
            $vars['app_id']		= (int) $app->alias->application->translateAliasToID($segments[1]);
            $vars['alpha_char'] = (string) $segments[2];
            $vars['page']       = (int) $segments[3];
        }

	// tag (with optional pagination)
	$task = 'tag';

		// if a menu item is set, avoid using app alias in the url
        if ($count == 2 && $segments[0] == $task && $menu_item = $app->menu->getActive()) {
            $vars['task']   = $task;
            $vars['app_id']	= $menu_item->params->get('application');
            $vars['tag']    = (string) $segments[1];
        }

        if ($count == 3 && $segments[0] == $task) {
            $vars['task']       = $task;
            if (!$vars['app_id'] = (int) $app->alias->application->translateAliasToID($segments[1]) and $menu_item = $app->menu->getActive()) {
                $vars['app_id'] = $menu_item->params->get('application');
                $vars['tag']    = (string) $segments[1];
                $vars['page']   = (int) $segments[2];
            } else {
                $vars['tag']    = (string) $segments[2];
            }
        }

        if ($count == 4 && $segments[0] == $task) {
            $vars['task']   = $task;
            $vars['app_id']	= (int) $app->alias->application->translateAliasToID($segments[1]);
            $vars['tag']    = (string) $segments[2];
            $vars['page']   = (int) $segments[3];
        }

	// item
	$task = 'item';

		if ($count == 2 && $segments[0] == $task) {
			$vars['task']    = $task;
			$vars['item_id'] = (int) $app->alias->item->translateAliasToID($segments[1]);
		}

	// feed
	$task = 'feed';

		if ($count == 3 && $segments[0] == $task) {
			$vars['task'] = $task;
			$vars['type'] = (string) $segments[1];
			$vars['app_id'] = (int) $app->alias->application->translateAliasToID($segments[2]);
		}

		if ($count == 4 && $segments[0] == $task) {
			$vars['task']        = $task;
			$vars['type']        = (string) $segments[1];
			$vars['app_id']		 = (int) $app->alias->application->translateAliasToID($segments[2]);
			$vars['category_id'] = (int) $app->alias->category->translateAliasToID($segments[3]);
		}

	// submission
	$task = 'submission';
    $layout = 'submission';

		if ($count == 2 && $segments[0] == $task && $segments[1] == $layout) {
			$vars['task']   = $task;
			$vars['layout'] = (string) $segments[1];
		}

		if ($count == 5 && $segments[0] == $task && $segments[1] == $layout) {
			$vars['task']            = $task;
			$vars['layout']          = (string) $segments[1];
			$vars['submission_id']   = (int) $app->alias->submission->translateAliasToID($segments[2]);
            $vars['type_id']         = (string) $segments[3];
            $vars['submission_hash'] = (string) $segments[4];
		}

		if ($count == 6 && $segments[0] == $task && $segments[1] == $layout) {
			$vars['task']            = $task;
			$vars['layout']          = (string) $segments[1];
			$vars['submission_id']   = (int) $app->alias->submission->translateAliasToID($segments[2]);
            $vars['type_id']         = (string) $segments[3];
            $vars['submission_hash'] = (string) $segments[4];
            $vars['item_id']         = (int) $app->alias->item->translateAliasToID($segments[5]);
		}

	// submission mysubmissions
	$task = 'submission';
    $layout = 'mysubmissions';

		if ($count == 2 && $segments[0] == $task && $segments[1] == $layout) {
			$vars['task']   = $task;
			$vars['layout'] = (string) $segments[1];
		}

		if ($count == 3 && $segments[0] == $task && $segments[1] == $layout) {
			$vars['task']          = $task;
			$vars['layout']        = (string) $segments[1];
			$vars['submission_id'] = (int) $app->alias->submission->translateAliasToID($segments[2]);
		}

	// try to retrieve vars from menu item
	if (empty($vars)) {
		if (($count == 0 || $count == 1 && is_numeric($segments[0])) && $menu_item = $app->menu->getActive()) {
			$vars['task'] = @$menu_item->query['view'];

			switch ($vars['task']) {
				case 'frontpage':
					if ($count == 1) {
						$vars['page'] = (int) $segments[0];
					}

					break;
				case 'category':
					if ($count == 1) {
						$vars['page'] = (int) $segments[0];
						$vars['category_id'] = @$menu_item->query['category_id'];
					}
					break;
			}
		}
	}

	$app->event->dispatcher->notify($app->event->create(null, 'application:sefparseroute', array('segments' => &$segments, 'vars' => &$vars)));

	return $vars;
}