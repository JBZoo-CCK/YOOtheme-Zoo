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

<?php if ($this->checkPosition('media')) : ?>
<div class="author-media"><?php echo $this->renderPosition('media'); ?></div>
<?php endif; ?>

<?php if ($this->checkPosition('title')) : ?>
<h3 class="name"><?php echo $this->renderPosition('title'); ?></h3>
<?php endif; ?>

<?php if ($this->checkPosition('description')) : ?>
<div class="description"><?php echo $this->renderPosition('description', array('style' => 'block')); ?></div>
<?php endif; ?>

<?php if ($this->checkPosition('links')) : ?>
<p class="author-links"><?php echo $this->renderPosition('links'); ?></p>
<?php endif;