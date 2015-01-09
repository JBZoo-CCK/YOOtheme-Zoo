<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// init lightbox
if (!empty($rel)) {
	$rel = 'data-lightbox="' . $rel .'"';

	$this->app->document->addScript('assets:js/lightbox.js');
	$this->app->document->addStylesheet('assets:css/lightbox.css');
	$this->app->document->addScriptDeclaration("jQuery(function($) { $('[data-lightbox]').lightbox(); });");
}

echo '<a href="'.JRoute::_($this->get('value', '')).'" title="'.$this->getTitle().'" '.$target.' '. $rel .'>'.$this->getText().'</a>';
