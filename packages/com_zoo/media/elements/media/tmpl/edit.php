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
		<?php echo $this->app->html->_('control.text', $this->getControlName('file'), $this->get('file'), 'placeholder="'.JText::_('File').'" class="file" readonly="readonly"'); ?>
    </div>

    <div class="row">
        <?php echo $this->app->html->_('control.text', $this->getControlName('url'), $this->get('url'), 'placeholder="'.JText::_('URL').'" class="url" size="50" maxlength="255" title="'.JText::_('URL').'"'); ?>
    </div>

	<div class="more-options">
		<div class="trigger">
			<div>
				<div class="file button"><?php echo JText::_('Video/Audio File'); ?></div>
				<div class="url button"><?php echo JText::_('Video Provider'); ?></div>
				<div class="advanced button hide"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">
			<div class="row short">
				<?php echo $this->app->html->_('control.text', $this->getControlName('width'), $this->get('width', $this->config->get('defaultwidth')), 'maxlength="4" title="'.JText::_('Width').'" placeholder="'.JText::_('Width').'"'); ?>
			</div>

			<div class="row short">
					<?php echo $this->app->html->_('control.text', $this->getControlName('height'), $this->get('height', $this->config->get('defaultheight')), 'maxlength="4" title="'.JText::_('Height').'" placeholder="'.JText::_('Height').'"'); ?>
			</div>

			<div class="row">
				<strong><?php echo JText::_('AutoPlay'); ?></strong>
				<?php echo $this->app->html->_('select.booleanlist', $this->getControlName('autoplay'), '', $this->get('autoplay', $this->config->get('defaultautoplay', false))); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(function($) {
		$('#<?php echo $this->identifier; ?> input[name="<?php echo $this->getControlName('file'); ?>"]').Directories({
			mode: 'file',
			url: '<?php echo $this->app->link(array('task' => 'callelement', 'format' => 'raw', 'type' => $this->_item->getType()->id, 'item_id' => $this->_item->id, 'elm_id' => $this->identifier, 'method' => 'files'), false); ?>',
			title: '<?php echo JText::_('Files'); ?>',
			msgDelete: '<?php echo JText::_('Delete'); ?>'
		});
		$('#<?php echo $this->identifier; ?>').ElementMedia();
	});
</script>