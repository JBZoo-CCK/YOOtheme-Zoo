<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

echo $this->app->html->_('zoo.countryselectlist', $this->app->country->getIsoToNameMapping(), $control_name.'[selectable_country][]', $parent->element->config->get('selectable_country', array()), true);