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

<div class="box">
	<div>

		<?php if ($this->checkPosition('media')) : ?>
		<div class="pos-media">
			<?php echo $this->renderPosition('media'); ?>
		</div>
		<?php endif; ?>

		<?php if ($this->checkPosition('title')) : ?>
		<h4 class="pos-title">
			<?php echo $this->renderPosition('title'); ?>
		</h4>
		<?php endif; ?>

		<?php if ($this->checkPosition('description')) : ?>
		<div class="pos-description">
			<?php echo $this->renderPosition('description', array('style' => 'block')); ?>
		</div>
		<?php endif; ?>

		<?php if ($this->checkPosition('links')) : ?>
		<p class="pos-links">
			<?php echo $this->renderPosition('links', array('style' => 'pipe')); ?>
		</p>
		<?php endif; ?>

	</div>
</div>

<?php if ($this->checkPosition('article')) : ?>
<div class="items">
	<?php echo $this->renderPosition('article', array('style' => 'block')); ?>
</div>
<?php endif;