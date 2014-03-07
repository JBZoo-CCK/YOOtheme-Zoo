<?php
/**
* @package   ZOO Tag
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('menuitem');

class JFormFieldZooMenuItem extends JFormFieldMenuItem {

	public $type = 'ZooMenuItem';

	protected function getGroups()	{

		// get app instance
		$app = App::getInstance('zoo');		
		
		// Merge the select item option into existing groups
		return array_merge(array(array($app->html->_('select.option', '', '- '.JText::_('Select Item').' -', 'value', 'text'))), parent::getGroups());

	}

}