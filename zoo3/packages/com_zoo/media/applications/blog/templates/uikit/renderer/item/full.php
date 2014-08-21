<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$params = $item->getParams('site');

/* set media alignment */
$align = ($this->checkPosition('media')) ? $view->params->get('template.item_media_alignment') : '';

?>

<?php if ($this->checkPosition('top')) : ?>
	<?php echo $this->renderPosition('top', array('style' => 'uikit_block')); ?>
<?php endif; ?>
<?php if ($align == "above") : ?>
	<?php echo $this->renderPosition('media', array('style' => 'uikit_block')); ?>
<?php endif; ?>

<?php if ($this->checkPosition('title')) : ?>
<h1 class="uk-article-title">
	<?php echo $this->renderPosition('title'); ?>
</h1>
<?php endif; ?>

<?php if ($this->checkPosition('subtitle')) : ?>
<p class="uk-article-lead">
	<?php echo $this->renderPosition('subtitle'); ?>
</p>
<?php endif; ?>

<?php if ($align == "top") : ?>
	<?php echo $this->renderPosition('media', array('style' => 'uikit_block')); ?>
<?php endif; ?>

<?php if ($align == "left" || $align == "right") : ?>
<div class="uk-align-medium-<?php echo $align; ?>">
	<?php echo $this->renderPosition('media'); ?>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('content')) : ?>
	<?php echo $this->renderPosition('content'); ?>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
<p class="uk-article-meta">
	<?php echo $this->renderPosition('meta'); ?>
</p>
<?php endif; ?>

<?php if ($align == "bottom") : ?>
	<?php echo $this->renderPosition('media', array('style' => 'uikit_block')); ?>
<?php endif; ?>

<?php if ($this->checkPosition('taxonomy')) : ?>
<ul class="uk-list">
	<?php echo $this->renderPosition('taxonomy', array('style' => 'uikit_list')); ?>
</ul>
<?php endif; ?>

<?php if ($this->checkPosition('bottom')) : ?>
	<?php echo $this->renderPosition('bottom', array('style' => 'uikit_block')); ?>
<?php endif; ?>

<?php if ($this->checkPosition('related')) : ?>
	<h3><?php echo JText::_('Related Articles'); ?></h3>
	<?php echo $this->renderPosition('related'); ?>
<?php endif; ?>

<?php if ($this->checkPosition('author')) : ?>
<div class="uk-panel uk-panel-box">
	<?php echo $this->renderPosition('author', array('style' => 'uikit_author')); ?>
</div>
<?php endif;