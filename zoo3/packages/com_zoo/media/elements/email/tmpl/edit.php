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

	<?php echo $this->app->html->_('control.text', $this->getControlname('value'), $this->get('value'), 'size="60" title="'.JText::_('Email').'"'); ?>

	<?php if ($trusted_mode) : ?>

	<div class="more-options">
		<div class="trigger">
			<div>
				<div class="advanced button hide"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">
			<div class="row">
				<?php echo $this->app->html->_('control.text', $this->getControlName('text'), $this->get('text'), 'size="60" title="'.JText::_('Link Text').'" placeholder="'.JText::_('Link Text').'"'); ?>
			</div>

			<div class="row">
				<?php echo $this->app->html->_('control.text', $this->getControlName('subject'), $this->get('subject'), 'size="60" title="'.JText::_('Subject').'" placeholder="'.JText::_('Subject').'"'); ?>
			</div>

			<div class="row">
				<?php echo $this->app->html->_('control.text', $this->getControlName('body'), $this->get('body'), 'size="60" title="'.JText::_('Body').'" placeholder="'.JText::_('Body').'"'); ?>
			</div>
		</div>
	</div>

	<?php endif; ?>

</div>