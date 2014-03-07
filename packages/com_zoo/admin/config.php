<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load framework
require_once(dirname(__FILE__).'/framework/config.php');

// set defines
define('ZOO_COPYRIGHT', '<div class="copyright"><a target="_blank" href="http://zoo.yootheme.com">ZOO</a> is developed by <a target="_blank" href="http://www.yootheme.com">YOOtheme</a>. All Rights Reserved.</div>');
define('ZOO_TABLE_APPLICATION', '#__zoo_application');
define('ZOO_TABLE_CATEGORY', '#__zoo_category');
define('ZOO_TABLE_CATEGORY_ITEM', '#__zoo_category_item');
define('ZOO_TABLE_COMMENT', '#__zoo_comment');
define('ZOO_TABLE_ITEM', '#__zoo_item');
define('ZOO_TABLE_RATING', '#__zoo_rating');
define('ZOO_TABLE_SEARCH', '#__zoo_search_index');
define('ZOO_TABLE_SUBMISSION', '#__zoo_submission');
define('ZOO_TABLE_TAG', '#__zoo_tag');
define('ZOO_TABLE_VERSION', '#__zoo_version');

// init vars
$zoo = App::getInstance('zoo');
$path = dirname(__FILE__);
$cache_path = JPATH_ROOT.'/cache/com_zoo';
$media_path = JPATH_ROOT.'/media/zoo';

// register paths
$zoo->path->register(JPATH_ROOT.'/modules', 'modules');
$zoo->path->register(JPATH_ROOT.'/plugins', 'plugins');
$zoo->path->register($zoo->system->config->get('tmp_path'), 'tmp');
$zoo->path->register($path.'/assets', 'assets');
$zoo->path->register($cache_path, 'cache');
$zoo->path->register($path.'/classes', 'classes');
$zoo->path->register($path, 'component.admin');
$zoo->path->register(JPATH_ROOT.'/components/com_zoo', 'component.site');
$zoo->path->register($path.'/controllers', 'controllers');
$zoo->path->register($path.'/events', 'events');
$zoo->path->register($path.'/helpers', 'helpers');
$zoo->path->register($path.'/installation', 'installation');
$zoo->path->register($path.'/joomla', 'joomla');
$zoo->path->register($path.'/joomla/elements', 'joomla.elements');
$zoo->path->register($path.'/libraries', 'libraries');
$zoo->path->register($media_path.'/applications', 'applications');
$zoo->path->register($media_path.'/assets', 'assets');
$zoo->path->register($media_path.'/elements', 'elements');
$zoo->path->register($media_path.'/libraries', 'libraries');
$zoo->path->register($path.'/partials', 'partials');
$zoo->path->register($path.'/tables', 'tables');
$zoo->path->register($path.'/installation/updates', 'updates');
$zoo->path->register($path.'/views', 'views');

// create cache folder if none existent
if (!JFolder::exists($cache_path) && $zoo->request->get('option', 'cmd') != 'com_cache') {
	JFolder::create($cache_path);
	$zoo->zoo->putIndexFile($cache_path);
}

// register classes
$zoo->loader->register('Application', 'classes:application.php');
$zoo->loader->register('Category', 'classes:category.php');
$zoo->loader->register('Comment', 'classes:comment.php');
$zoo->loader->register('CommentAuthor', 'classes:commentauthor.php');
$zoo->loader->register('CommentAuthorJoomla', 'classes:commentauthor.php');
$zoo->loader->register('CommentAuthorFacebook', 'classes:commentauthor.php');
$zoo->loader->register('CommentAuthorTwitter', 'classes:commentauthor.php');
$zoo->loader->register('Item', 'classes:item.php');
$zoo->loader->register('ItemRenderer', 'classes:renderer/item.php');
$zoo->loader->register('Submission', 'classes:submission.php');

// register and connect events
$zoo->event->register('ApplicationEvent');
$zoo->event->dispatcher->connect('application:init', array('ApplicationEvent', 'init'));

$zoo->event->register('ItemEvent');
$zoo->event->dispatcher->connect('item:saved', array('ItemEvent', 'saved'));
$zoo->event->dispatcher->connect('item:deleted', array('ItemEvent', 'deleted'));
$zoo->event->dispatcher->connect('item:stateChanged', array('ItemEvent', 'stateChanged'));

$zoo->event->register('CategoryEvent');
$zoo->event->dispatcher->connect('category:saved', array('CategoryEvent', 'saved'));
$zoo->event->dispatcher->connect('category:deleted', array('CategoryEvent', 'deleted'));
$zoo->event->dispatcher->connect('category:stateChanged', array('CategoryEvent', 'stateChanged'));

$zoo->event->register('CommentEvent');
$zoo->event->dispatcher->connect('comment:saved', array('CommentEvent', 'saved'));
$zoo->event->dispatcher->connect('comment:deleted', array('CommentEvent', 'deleted'));
$zoo->event->dispatcher->connect('comment:stateChanged', array('CommentEvent', 'stateChanged'));

$zoo->event->register('SubmissionEvent');
$zoo->event->dispatcher->connect('submission:saved', array('SubmissionEvent', 'saved'));

$zoo->event->register('LayoutEvent');
$zoo->event->dispatcher->connect('layout:init', array('LayoutEvent', 'init'));

$zoo->event->register('TypeEvent');
$zoo->event->dispatcher->connect('type:beforesave', array('TypeEvent', 'beforesave'));
$zoo->event->dispatcher->connect('type:copied', array('TypeEvent', 'copied'));
$zoo->event->dispatcher->connect('type:deleted', array('TypeEvent', 'deleted'));

$zoo->event->register('ElementEvent');
$zoo->event->dispatcher->connect('element:configform', array('ElementEvent', 'configForm'));
