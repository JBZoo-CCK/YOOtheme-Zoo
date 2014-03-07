<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG;
if (class_exists('shRouter')) {
	$sefConfig = shRouter::shGetConfig();
} else {
	$sefConfig = Sh404sefFactory::getConfig();
}
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// ------------------  load language file - adjust as needed ----------------------------------------
//$shLangIso = shLoadPluginLanguage( 'com_XXXXX', $shLangIso, '_SEF_SAMPLE_TEXT_STRING');
// ------------------  load language file - adjust as needed ----------------------------------------

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');
if (!empty($limit))
shRemoveFromGETVarsList('limit');
if (isset($limitstart))
  shRemoveFromGETVarsList('limitstart'); // limitstart can be zero

// start by inserting the menu element title (just an idea, this is not required at all)
$task = isset($task) ? @$task : null;
$Itemid = isset($Itemid) ? @$Itemid : null;
$shSampleName = shGetComponentPrefix($option);
$shSampleName = empty($shSampleName) ?
		getMenuTitle($option, $task, $Itemid, null, $shLangName) : $shSampleName;
$shSampleName = (empty($shSampleName) || $shSampleName == '/') ? 'SampleCom':$shSampleName;

// ZOO ZOO ZOO ZOO ZOO ZOO ZOO ZOO

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

// get ZOO app
$zoo = App::getInstance('zoo');

// get query parameters
$uri = new JURI($string);
$query = $uri->getQuery(true);

// if task is empty get task from view parameter
$task = !empty($task) ? $task : (isset($query['view']) ? $query['view'] : null);

$controller = isset($query['controller']) ? $query['controller'] : null;

// ignore ajax requests
if (in_array($task, array('remove', 'callelement', 'element')) || in_array($controller, array('comment', 'item'))) {
	$dosef = false;
}

switch ($task) {

	case 'alphaindex':
		$title[] = $task;
		$title[] = $zoo->alias->application->translateIDToAlias((int) $query['app_id']);
		$title[] = $query['alpha_char'];

		shRemoveFromGETVarsList('app_id');
		shRemoveFromGETVarsList('alpha_char');

		// pagination
		if (isset($query['page'])) {
			$title[] = $query['page'];
			shRemoveFromGETVarsList('page');
		}
		break;

	case 'category':

		// retrieve item id from menu item
		if (!isset($query['category_id'])) {
			$query['category_id'] = $zoo->object->create('JSite')->getMenu()->getParams($Itemid)->get('category');
		}

		$title[] = $task;
		$title[] = $zoo->alias->category->translateIDToAlias((int) $query['category_id']);

		// pagination
		if (isset($query['page'])) {
			$title[] = $query['page'];
			shRemoveFromGETVarsList('page');
		}

		shRemoveFromGETVarsList('category_id');
		break;

	case 'feed':
		$title[] = $task;
		$title[] = $query['type'];
		$title[] = $zoo->alias->application->translateIDToAlias((int) $query['app_id']);
		$title[] = $zoo->alias->category->translateIDToAlias((int) $query['category_id']);

		shRemoveFromGETVarsList('type');
		shRemoveFromGETVarsList('app_id');
		shRemoveFromGETVarsList('category_id');
		break;

	case 'frontpage':

		// retrieve app id from menu item
		if (!isset($query['app_id'])) {
			$query['app_id'] = $zoo->object->create('JSite')->getMenu()->getParams($Itemid)->get('application');
		}

		$title[] = $task;
		$title[] = $zoo->alias->application->translateIDToAlias($query['app_id']);

		// pagination
		if (isset($query['page'])) {
			$title[] = $query['page'];
			shRemoveFromGETVarsList('page');
		}

		break;

	case 'item':

		// retrieve item id from menu item
		if (!isset($query['item_id'])) {
			$query['item_id'] = $zoo->object->create('JSite')->getMenu()->getParams($Itemid)->get('item_id');
		}

		$title[] = $task;
		$title[] = $zoo->alias->item->translateIDToAlias((int) $query['item_id']);

		shRemoveFromGETVarsList('item_id');
		break;

	case 'submission':

		// get menu
		$menu_params = $zoo->object->create('JSite')->getMenu()->getParams($Itemid);

		// retrieve item id from menu item
		if (!isset($query['submission_id'])) {
			$query['submission_id'] = $menu_params->get('submission');
			$query['type_id'] = $menu_params->get('type');
			$query['item_id'] = $menu_params->get('item_id');
			$query['submission_hash'] = '';
		}

		if ($query['layout'] == 'submission') {

			$title[] = $task;
			$title[] = $query['layout'];
			$title[] = $zoo->alias->submission->translateIDToAlias((int) $query['submission_id']);
			$title[] = $query['type_id'];
			$title[] = $query['submission_hash'];
			$title[] =  $zoo->alias->item->translateIDToAlias((int) @$query['item_id']);

			$title = array_filter($title);

			shRemoveFromGETVarsList('layout');
			shRemoveFromGETVarsList('submission_id');
			shRemoveFromGETVarsList('type_id');
			shRemoveFromGETVarsList('submission_hash');
			shRemoveFromGETVarsList('item_id');

		} else if ($query['layout'] == 'mysubmissions') {

			$title[] = $task;
            $title[] = $query['layout'];
			$title[] = $zoo->alias->submission->translateIDToAlias((int) $query['submission_id']);

			shRemoveFromGETVarsList('layout');
			shRemoveFromGETVarsList('submission_id');

		}

		break;

	case 'tag':
		$title[] = $task;
		$title[] = $zoo->alias->application->translateIDToAlias((int) $query['app_id']);
		$title[] = $query['tag'];

		shRemoveFromGETVarsList('app_id');
		shRemoveFromGETVarsList('tag');

		// pagination
		if (isset($query['page'])) {
			$title[] = $query['page'];
			shRemoveFromGETVarsList('page');
		}
		break;

	default:
		// trigger sh404sef event
		$zoo->event->dispatcher->notify($zoo->event->create(null, 'application:sh404sef', array('title' => &$title, 'query' => &$query, 'dosef' => &$dosef)));

}

shRemoveFromGETVarsList('task');
shRemoveFromGETVarsList('view');
shRemoveFromGETVarsList('layout');

// ZOO ZOO ZOO ZOO ZOO ZOO ZOO ZOO

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef) {
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
      (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------

