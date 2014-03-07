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

	<div class="download-select">

		<div class="upload">
			<input type="text" id="filename<?php echo $this->identifier; ?>" readonly="readonly" />
			<div class="button-container">
				<button class="button-grey search" type="button"><?php echo JText::_('Search'); ?></button>
				<input type="file" name="elements_<?php echo $this->identifier; ?>" onchange="javascript: document.getElementById('filename<?php echo $this->identifier; ?>').value = this.value.replace(/^.*[\/\\]/g, '');" />
			</div>
		</div>

		<?php if (isset($lists['upload_select'])) : ?>

			<span class="select"><?php echo JText::_('ALREADY UPLOADED'); ?></span><?php echo $lists['upload_select']; ?>

		<?php else : ?>

			<input type="hidden" class="upload" name="<?php echo $this->getControlName('upload'); ?>" value="<?php echo $upload ? 1 : ''; ?>" />

        <?php endif; ?>

    </div>

    <div class="download-preview">
        <span class="preview"><?php echo $upload; ?></span>
        <span class="download-cancel" title="<?php JText::_('Remove file'); ?>"></span>
    </div>

    <?php if ($trusted_mode) : ?>

	<div class="more-options">
		<div class="trigger">
			<div>
				<div class="advanced button hide"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">

			<div class="row short download-limit">
				<?php echo $this->app->html->_('control.text', $this->getControlName('download_limit'), ($upload ? $this->get('download_limit') : ''), 'maxlength="255" title="'.JText::_('Download limit').'" placeholder="'.JText::_('Download limit').'"'); ?>
			</div>

		</div>
	</div>
    <?php endif; ?>

    <script type="text/javascript">
		jQuery(function($) {
			$('#<?php echo $this->identifier; ?>').DownloadSubmission();
		});
    </script>

</div>