<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div>

    <div class="row">
        <?php echo $this->app->html->_('control.text', $this->getControlName('value'), $this->get('value'), 'maxlength="255" title="'.JText::_('Tags').'" placeholder="'.JText::_('Tags').'"'); ?>
    </div>

    <div class="row">
        <?php echo $this->app->html->_('control.text', $this->getControlName('flickrid'), $this->get('flickrid'), 'maxlength="255" title="'.JText::_('Flickr ID').'" placeholder="'.JText::_('Flickr ID').'"'); ?>
    </div>

</div>