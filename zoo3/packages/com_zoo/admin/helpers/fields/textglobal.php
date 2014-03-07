<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// load js
$this->app->document->addScript('fields:global.js');

// init vars
$id      = uniqid('radiotext-');
$global = $parent->getValue((string) $name) === null;

// create html
echo '<div class="global text">';
echo '<input id="'.$id.'" type="checkbox" name="_global"'.($global ? ' checked="checked"' : '').' />';
echo '<label for="'.$id.'">'.JText::_('Global').'</label>';
echo '<div class="input">';
echo $this->app->field->render('text', $name, $value, $node, compact('control_name', 'parent'));
echo '</div>';
echo '</div>';