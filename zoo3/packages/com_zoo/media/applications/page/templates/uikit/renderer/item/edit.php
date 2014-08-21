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

<fieldset>
	<legend><?php echo $item->getType()->name; ?></legend>

	<?php if ($this->checkPosition('content')) : ?>
	<?php echo $this->renderPosition('content', array('style' => 'submission.uikit_row')); ?>
	<?php endif; ?>

</fieldset>

<?php if ($this->checkPosition('media')) : ?>
<fieldset>
	<legend><?php echo JText::_('Media'); ?></legend>

	<?php echo $this->renderPosition('media', array('style' => 'submission.uikit_row')); ?>

</fieldset>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
<fieldset>
	<legend><?php echo JText::_('Meta'); ?></legend>

	<?php echo $this->renderPosition('meta', array('style' => 'submission.uikit_row')); ?>

</fieldset>
<?php endif; ?>

<?php if ($this->checkPosition('administration')) : ?>
<fieldset>
	<legend><?php echo JText::_('Administration'); ?></legend>

	<?php echo $this->renderPosition('administration', array('style' => 'submission.uikit_row')); ?>

</fieldset>
<?php endif;