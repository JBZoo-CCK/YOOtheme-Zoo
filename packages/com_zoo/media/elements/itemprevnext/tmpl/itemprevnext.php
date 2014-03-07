<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->document->addStylesheet('elements:itemprevnext/itemprevnext.css');

?>

<div class="page-nav clearfix">
	<?php if ($prev_link) : ?>
	<a class="prev" href="<?php echo $prev_link; ?>"><?php echo JText::_('JGLOBAL_LT').' '.JText::_('JPREV'); ?></a>
	<?php endif; ?>

	<?php if ($next_link) : ?>
	<a class="next" href="<?php echo $next_link; ?>"><?php echo JText::_('JNEXT').' '.JText::_('JGLOBAL_GT'); ?></a>
	<?php endif; ?>
</div>