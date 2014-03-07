<?php
/**
* @package   ZOO Tag
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$count = count($tags);

?>

<?php if ($count) : ?>

<ul class="uk-list">
	<?php $i = 0; foreach ($tags as $tag) : ?>
	<li class="weight<?php echo $tag->weight; ?>">
		<a href="<?php echo JRoute::_($tag->href); ?>"><?php echo $tag->name; ?></a>
	</li>
	<?php $i++; endforeach; ?>
</ul>

<?php else : ?>
<?php echo JText::_('COM_ZOO_NO_TAGS_FOUND'); ?>
<?php endif;