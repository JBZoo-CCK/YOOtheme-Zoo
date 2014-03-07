<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// set attributes
$attributes = array('type' => 'hidden', 'name' => "{$control_name}[{$name}]", 'value' => $value);

printf('<input %s />', $this->app->field->attributes($attributes, array('description', 'default')));