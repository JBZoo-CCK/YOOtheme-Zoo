<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// set attributes
$attributes = array();
$attributes['name']  = "{$control_name}[{$name}]";
$attributes['class'] = isset($node->attributes()->class) ? (string) $node->attributes()->class : '';

printf('<textarea %s>%s</textarea>', $this->app->field->attributes($attributes, array('label', 'description', 'default')), $value);