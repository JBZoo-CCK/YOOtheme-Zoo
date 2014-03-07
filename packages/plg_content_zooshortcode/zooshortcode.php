<?php
/**
* @package   Content - ZOO Shortcode
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgContentZooshortcode extends JPlugin {

	public $app;

	public function onPrepareContent(&$row, &$params, $page=0) {
		return $this->_prepareContent($row, $params, $page);
	}

	public function onContentPrepare($context, &$article, &$params, $page = 0) {
		return $this->_prepareContent($article, $params, $page);
	}

	protected function _prepareContent(&$article, &$params, $page = 0) {

		// simple performance check to determine whether text should be processed further
		if (strpos($article->text, 'zooitem') === false && strpos($article->text, 'zoocategory') === false) {
			return true;
		}

		// load ZOO config
		jimport('joomla.filesystem.file');
		if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php') || !JComponentHelper::getComponent('com_zoo', true)->enabled) {
			return;
		}
		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		// Get the ZOO App instance
		$this->app = App::getInstance('zoo');

		$this->_doReplacement($article, 'item');
		$this->_doReplacement($article, 'category');

		return true;

	}

	protected function _doReplacement(&$article, $name) {
		// expression to search for
		$regex		= '/{zoo'.$name.':\s*(\S*)(?:\s*text:\s*(.*?))?}/i';
		$matches	= array();

		// find all instances of plugin and put in $matches
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {

			// $match[0] is full pattern match, $match[1] is the item id or alias
			$id = $match[1];
			if (!is_numeric($match[1])) {
				$id = $this->app->alias->$name->translateAliasToID($match[1]);
			}

			if ($id && ($object = $this->app->table->$name->get($id))) {

				if (isset($match[2]) && !empty($match[2])) {
					$text = $match[2];
				} else {
					$text = $object->name;
				}

				$output = '<a title="'.$object->name.'" href="'.$this->app->route->$name($object).'">'.$text.'</a>';
			} else {
				$output = '';
			}

			// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
			$article->text = preg_replace("|$match[0]|", $output, $article->text, 1);

		}

		return true;

	}

}