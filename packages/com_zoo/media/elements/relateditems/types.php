<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get element from parent parameter form
$config  	 = $parent->element->config;
$application = $parent->application;

// init vars
$attributes = array();
$attributes['class'] = (string) $node->attributes()->class ? (string) $node->attributes()->class : 'inputbox';
$attributes['multiple'] = 'multiple';
$attributes['size'] = (string) $node->attributes()->size ? (string) $node->attributes()->size : '';

foreach ($application->getTypes() as $type) {
	$options[] = $this->app->html->_('select.option', $type->id, JText::_($type->name));
}

echo $this->app->html->_('select.genericlist', $options, $control_name.'[selectable_types][]', $attributes, 'value', 'text', $config->get('selectable_types', array()));