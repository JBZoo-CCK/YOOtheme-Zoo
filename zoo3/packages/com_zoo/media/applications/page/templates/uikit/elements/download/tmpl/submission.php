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

		<div class="zo-upload">
			<input type="text" id="filename<?php echo $this->identifier; ?>" readonly="readonly" />
            <div class="zo-button-container">
    			<button class="uk-button search" type="button"><?php echo JText::_('Search'); ?></button>
    			<input type="file" name="elements_<?php echo $this->identifier; ?>" onchange="javascript: document.getElementById('filename<?php echo $this->identifier; ?>').value = this.value.replace(/^.*[\/\\]/g, '');" />
            </div>
		</div>

		<?php if (isset($lists['upload_select'])) : ?>

			<span class="select"><?php echo JText::_('ALREADY UPLOADED'); ?></span><?php echo $lists['upload_select']; ?>

		<?php else : ?>

			<input type="hidden" class="upload" name="<?php echo $this->getControlName('upload'); ?>" value="<?php echo $upload ? 1 : ''; ?>" />

        <?php endif; ?>

    </div>

    <div class="uk-margin download-preview">
        <span class="preview"><?php echo $upload; ?></span>
        <span class="download-cancel" title="<?php JText::_('Remove file'); ?>"><i class="uk-icon-times"></i></span>
    </div>

    <?php if ($trusted_mode) : ?>

	<div class="more-options">
		<div class="trigger zo-absolute">
			<div>
				<div class="advanced button hide uk-button uk-button-mini"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button uk-button uk-button-mini"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">

			<div class="uk-margin row short download-limit">
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