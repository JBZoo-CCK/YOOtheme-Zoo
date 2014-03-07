<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$target = $target ? 'target="_blank"' : '';
$rel	= $rel ? 'rel="' . $rel .'"' : '';
$title  = $title ? ' title="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'"' : '';

$link_enabled = !empty($url);

$info = getimagesize($file);
$content = '<img src="'.$link.'"'.$title.' alt="'.$alt.'" '.$info[3].' />';

if ($link_enabled) {
	echo '<a href="'.JRoute::_($url).'" '.$rel.$title.$target.'>'.$content.'</a>';
} else {
	echo $content;
}