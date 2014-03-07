<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// load js
$this->app->document->addScript('assets:js/image.js');

// init vars
$width 	= $parent->getValue($name.'_width');
$height = $parent->getValue($name.'_height');

// create image select html
$html[] = '<input class="image-select" type="text" name="'.$control_name.'['.$name.']'.'" value="'.$value.'" />';
$html[] = '<div class="image-measures">';
$html[] = JText::_('Width').' <input type="text" name="'.$control_name.'['.$name.'_width]'.'" value="'.$width.'" style="width:30px;" />';
$html[] = JText::_('Height').' <input type="text" name="'.$control_name.'['.$name.'_height]'.'" value="'.$height.'" style="width:30px;" />';
$html[] = '</div>';

echo implode("\n", $html);