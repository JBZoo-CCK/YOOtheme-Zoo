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

$day = $this->app->html->_('date', $item->created, $this->app->date->format('%d'), $this->app->date->getOffset());
$month = $this->app->html->_('date', $item->created, $this->app->date->format('%B'), $this->app->date->getOffset());

?>

<?php if (($params->get('template.teaseritem_media_alignment') == "above") && $this->checkPosition('media')) : ?>
<div class="pos-media media-<?php echo $params->get('template.teaseritem_media_alignment'); ?>">
	<?php echo $this->renderPosition('media', array('style' => 'block')); ?>
</div>
<?php endif; ?>

<div class="pos-date">
	<span class="day"><?php echo JText::_($day); ?></span><span class="month"><?php echo JText::_($month); ?></span>
</div>

<?php if ($this->checkPosition('title')) : ?>
<h1 class="pos-title">
	<?php echo $this->renderPosition('title'); ?>
</h1>
<?php endif; ?>

<?php if ($this->checkPosition('subtitle')) : ?>
<h2 class="pos-subtitle">
	<?php echo $this->renderPosition('subtitle'); ?>
</h2>
<?php endif; ?>

<?php if (($params->get('template.teaseritem_media_alignment') == "top") && $this->checkPosition('media')) : ?>
<div class="pos-media media-<?php echo $params->get('template.teaseritem_media_alignment'); ?>">
	<?php echo $this->renderPosition('media', array('style' => 'block')); ?>
</div>
<?php endif; ?>

<div class="floatbox">

	<?php if ((($params->get('template.teaseritem_media_alignment') == "left") || ($params->get('template.teaseritem_media_alignment') == "right")) && $this->checkPosition('media')) : ?>
	<div class="pos-media media-<?php echo $params->get('template.teaseritem_media_alignment'); ?>">
		<?php echo $this->renderPosition('media', array('style' => 'block')); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->checkPosition('content')) : ?>
	<div class="pos-content">
		<?php echo $this->renderPosition('content', array('style' => 'block')); ?>
	</div>
	<?php endif; ?>

</div>

<?php if (($params->get('template.teaseritem_media_alignment') == "bottom") && $this->checkPosition('media')) : ?>
<div class="pos-media media-<?php echo $params->get('template.teaseritem_media_alignment'); ?>">
	<?php echo $this->renderPosition('media', array('style' => 'block')); ?>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('links')) : ?>
<p class="pos-links">
	<?php echo $this->renderPosition('links', array('style' => 'pipe')); ?>
</p>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
<p class="pos-meta">
	<?php echo $this->renderPosition('meta', array('style' => 'comma')); ?>
</p>
<?php endif;