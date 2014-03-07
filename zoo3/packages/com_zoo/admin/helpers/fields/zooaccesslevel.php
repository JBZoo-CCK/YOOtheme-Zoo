<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// init vars
$attr  = '';
$attr .= (string) $node->attributes()->class ? 'class="'.$node->attributes()->class.'"' : 'class="inputbox"';
$attr .= ((string) $node->attributes()->disabled == 'true') ? ' disabled="disabled"' : '';
$attr .= (string) $node->attributes()->size ? ' size="'.(int) $node->attributes()->size.'"' : '';
$attr .= ((string) $node->attributes()->multiple == 'true') ? ' multiple="multiple"' : '';

// Initialize JavaScript field attributes.
$attr .= (string) $node->attributes()->onchange ? ' onchange="'.(string) $node->attributes()->onchange.'"' : '';

echo $this->app->html->_('zoo.accesslevel', array(), $control_name.'['.$name.']', $attr, 'value', 'text', $value, $control_name.$name);