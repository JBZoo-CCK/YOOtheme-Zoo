<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->document->addScript('libraries:jquery/jquery-ui.custom.min.js');
$this->app->document->addStylesheet('libraries:jquery/jquery-ui.custom.css');
$this->app->document->addScript('libraries:jquery/plugins/timepicker/timepicker.js');
$this->app->document->addStylesheet('libraries:jquery/plugins/timepicker/timepicker.css');
$this->app->document->addStylesheet($this->template->resource.'assets/css/submission.css');
$this->app->document->addScript('assets:js/submission.js');
$this->app->document->addScript('assets:js/placeholder.js');
$this->app->document->addScript('assets:js/item.js');

if ($this->submission->showTooltip()) {
	$this->app->html->_('behavior.tooltip');
}

?>

<?php if ($this->errors): ?>
	<?php $msg = count($this->errors) > 1 ? JText::_('Oops. There were errors in your submission.') : JText::_('Oops. There was an error in your submission.'); ?>
	<?php $msg .= ' '.JText::_('Please take a look at all highlighted fields, correct your data and try again.'); ?>
	<div class="uk-alert"><?php echo $msg; ?></div>
<?php endif; ?>

<form id="item-submission" class="uk-form" action="<?php echo JRoute::_($this->app->route->submission($this->submission, $this->type->id, null, $this->item->id, $this->redirectTo)); ?>" method="post" name="submissionForm" accept-charset="utf-8" enctype="multipart/form-data">

	<?php

		echo $this->renderer->render($this->layout_path, array('item' => $this->item, 'submission' => $this->submission));

		// Captcha support
		if ($this->captcha) {
			$this->app->html->_('behavior.framework');
			echo $this->captcha->display('captcha', 'captcha', 'captcha');
		}
	?>

	<div class="uk-alert"><?php echo JText::_('REQUIRED_INFO'); ?></div>

	<div class="uk-margin">
		<button type="submit" id="submit-button" class="uk-button"><?php echo $this->item->id ? JText::_('Save') : JText::_('Submit Item'); ?></button>
		<?php if ($this->cancelUrl) : ?>
			<a href="<?php echo JRoute::_($this->cancelUrl); ?>" id="cancel-button" class="uk-button uk-button-link"><?php echo JText::_('Cancel'); ?></a>
		<?php endif; ?>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->app->component->self->name; ?>" />
	<input type="hidden" name="controller" value="submission" />
	<input type="hidden" name="task" value="save" />

	<?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($) {
		$('#item-submission').EditItem();
		$('#item-submission').Submission({ uri: '<?php echo JURI::root(); ?>' });
	});
</script>