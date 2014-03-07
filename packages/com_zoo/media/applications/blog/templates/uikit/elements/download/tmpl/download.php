<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets css
$this->app->document->addStylesheet('elements:download/assets/css/download.css');

switch ($display) {
	case 'download_limit':
		$download_limit = ($download_limit) ? $download_limit : '-';
		echo $download_limit;
		break;

	case 'filesize':
		echo '<span>'.$size.'</span>';
		break;

	case 'filehits':
		echo '<span>'.$hits.'</span>';
		break;

	case 'buttonlink':
		if ($limit_reached) {
			echo '<a class="uk-button" href="javascript:alert(\''.JText::_('Download limit reached').'\');" title="'.JText::_('Download limit reached').'">'.JText::_('Download').'</a>';
		} else {
			echo '<a class="uk-button uk-button-primary" href="'.JRoute::_($download_link).'" title="'.$download_name.'">'.JText::_('Download').'</a>';
		}
		break;

	case 'imagelink':
		if ($limit_reached) {
			echo '<div class="zo-element-download-type-'.$filetype.'" title="'.JText::_('Download limit reached').'"></div>';
		} else {
			echo '<a class="zo-element-download-type-'.$filetype.'" href="'.JRoute::_($download_link).'" title="'.$download_name.'"></a>';
		}
		break;

	default:
		if ($limit_reached) {
			echo JText::_('Download limit reached');
		} else {
			echo '<a href="'.JRoute::_($download_link).'" title="'.$download_name.'">'.$download_name.'</a>';
		}
}