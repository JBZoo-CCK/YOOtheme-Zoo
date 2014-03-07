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

<div id="<?php echo $this->identifier; ?>">

    <div class="row">
		<input type="text" readonly="readonly" name="<?php echo $this->getControlName('file'); ?>" value="<?php echo $this->get('file'); ?>" placeholder="<?php echo JText::_('File'); ?>"/>
    </div>

	<div class="more-options">

		<div class="reset-container">
			<?php echo $info; ?>
			<?php if ($hits) : ?>
				<input name="reset-hits" type="button" class="button" value="<?php echo JText::_('Reset'); ?>"/>
			<?php endif; ?>
			<input name="<?php echo $this->getControlName('hits'); ?>" type="hidden" value="<?php echo $hits; ?>"/>
		</div>

		<div class="trigger">
			<div>
				<div class="advanced button hide"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">
			<div class="row short download-limit">
				<?php echo $this->app->html->_('control.text', $this->getControlName('download_limit'), $this->get('download_limit'), 'size="6" maxlength="255" title="'.JText::_('Download limit').'" placeholder="'.JText::_('Download Limit').'"'); ?>
			</div>
		</div>
	</div>

    <script type="text/javascript">
		jQuery(function($) {
			$('#<?php echo $this->identifier; ?>').ElementDownload( {
				url: "<?php echo $this->app->link(array('controller' => 'item', 'format' => 'raw', 'type' => $this->getType()->id, 'elm_id' => $this->identifier, 'item_id' => $this->getItem()->id), false); ?>"
			});
			$('#<?php echo $this->identifier; ?> input[name="<?php echo $this->getControlName('file'); ?>"]').Directories({
				mode: 'file',
				url: '<?php echo $this->app->link(array('task' => 'callelement', 'format' => 'raw', 'type' => $this->_item->getType()->id, 'item_id' => $this->_item->id, 'elm_id' => $this->identifier, 'method' => 'files'), false); ?>',
				title: '<?php echo JText::_('Files'); ?>',
				msgDelete: '<?php echo JText::_('Delete'); ?>'
			});
		});
    </script>

</div>