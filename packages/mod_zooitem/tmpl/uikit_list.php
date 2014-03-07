<?php
/**
* @package   ZOO Item
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$css_class = $application->getGroup().'-'.$application->getTemplate()->name;

?>

<?php if (!empty($items)) : ?>

<ul class="uk-list uk-list-line <?php echo $css_class ?>">
	<?php $i = 0; foreach ($items as $item) : ?>
	<li class="uk-clearfix"><?php echo $renderer->render('item.'.$layout, compact('item', 'params')); ?></li>
	<?php $i++; endforeach; ?>
</ul>

<?php else : ?>
<?php echo JText::_('COM_ZOO_NO_ITEMS_FOUND'); ?>
<?php endif;