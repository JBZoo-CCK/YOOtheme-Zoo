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

<div id="<?php echo $this->identifier; ?>" class="repeat-elements">
	<ul class="repeatable-list">

		<?php $this->rewind(); ?>
		<?php foreach($this as $self) : ?>
			<li class="repeatable-element">
				<?php echo $this->$function($params); ?>
			</li>
		<?php endforeach; ?>

		<?php $this->rewind(); ?>

		<li class="repeatable-element hidden">
			<?php echo preg_replace('/(elements\[\S+])\[(\d+)\]/', '$1[-1]', $this->$function($params)); ?>
		</li>

	</ul>
	<p class="add uk-margin-remove">
		<a href="javascript:void(0);"><i class="uk-icon-plus-circle"></i> <?php echo JText::sprintf('Add another %s', JText::_($this->app->string->ucfirst($this->getElementType()))); ?></a>
	</p>
</div>

<script type="text/javascript">
	jQuery('#<?php echo $this->identifier; ?>').ElementRepeatable({ msgDeleteElement : '<?php echo JText::_('Delete Element'); ?>', msgSortElement : '<?php echo JText::_('Sort Element'); ?>' });
</script>