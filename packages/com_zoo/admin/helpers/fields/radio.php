<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

foreach ($node->children() as $option) {

	// set attributes
	$id = uniqid('radio-');
	$attributes = array('id' => $id, 'type' => 'radio', 'name' => "{$control_name}[{$name}]", 'value' => $option->attributes()->value);

	// is checked ?
	if ($option->attributes()->value == $value) {
		$attributes = array_merge($attributes, array('checked' => 'checked'));
	}

	printf('<input %s /> <label %s>%s</label> ', $this->app->field->attributes($attributes), $this->app->field->attributes(array('for' => $id)), JText::_((string) $option));
}