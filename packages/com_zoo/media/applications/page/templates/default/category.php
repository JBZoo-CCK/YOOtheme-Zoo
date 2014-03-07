<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->error->raiseWarning(0, JText::_('Error Displaying Layout').' (The Pages App does not support a "'.$this->getLayout().'" view. It should display static content only. Please use another app.)');