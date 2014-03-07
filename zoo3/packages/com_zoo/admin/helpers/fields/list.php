<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

printf('<select %s>', $this->app->field->attributes(array('name' => "{$control_name}[{$name}]")));

foreach ($node->children() as $option) {

	// set attributes
	$attributes = array('value' => $option->attributes()->value);

	// is checked ?
	if ($option->attributes()->value == $value) {
		$attributes['selected'] = 'selected';
	}

	printf('<option %s>%s</option>', $this->app->field->attributes($attributes), JText::_((string) $option));
}

printf('</select>');