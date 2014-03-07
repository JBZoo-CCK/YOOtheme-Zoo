<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

	defined('_JEXEC') or die('Restricted access');

	// add script
	$this->app->document->addScript('assets:js/alias.js');
	$this->app->document->addScript('assets:js/submission.js');

	// filter output
	JFilterOutput::objectHTMLSafe($this->submission, ENT_QUOTES, array('params'));

?>

<form action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

	<?php echo $this->partial('menu'); ?>

	<div class="box-bottom">
		<div class="col col-left width-60">

			<fieldset class="creation-form">
				<legend><?php echo JText::_('Details'); ?></legend>
				<div class="element element-name">
					<strong><?php echo JText::_('Name'); ?></strong>
					<div id="name-edit">
						<div class="row">
							<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->submission->name; ?>" />
							<span class="message-name"><?php echo JText::_('Please enter valid name.'); ?></span>
						</div>
						<div class="slug">
							<span><?php echo JText::_('Slug'); ?>:</span>
							<a class="trigger" href="#" title="<?php echo JText::_('Edit Submission Slug');?>"><?php echo $this->submission->alias; ?></a>
							<div class="panel">
								<input type="text" name="alias" value="<?php echo $this->submission->alias; ?>" />
								<input type="button" class="accept" value="<?php echo JText::_('Accept'); ?>"/>
								<a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
							</div>
						</div>
					</div>
				</div>
				<div class="element element-published">
					<strong><?php echo JText::_('Published'); ?></strong>
					<?php echo $this->lists['select_published']; ?>
				</div>
				<div class="element element-tooltip">
					<strong><?php echo JText::_('Tooltip'); ?></strong>
					<?php echo $this->lists['select_tooltip']; ?>
				</div>
				<div class="element element-notification">
					<strong class="hasTip" title="<?php echo JText::_('EMAIL_NOTIFICATION_DESCRIPTION'); ?>"><?php echo JText::_('Email Notification'); ?></strong>
					<input type="text" name="params[email_notification]" value="<?php echo $this->submission->getParams()->get('email_notification', ''); ?>" />
				</div>
				<div class="element element-item-edit">
					<strong class="hasTip" title="<?php echo JText::_('ITEM_EDIT_DESCRIPTION'); ?>"><?php echo JText::_('Item Edit'); ?></strong>
					<?php echo $this->lists['select_item_edit']; ?>
				</div>
				<?php if($this->lists['select_item_captcha']): ?>
				<div class="element element-item-captcha">
					<strong class="hasTip" title="<?php echo JText::_('CAPTCHA_DESCRIPTION'); ?>"><?php echo JText::_('Use Captcha'); ?></strong>
					<div>
						<?php echo $this->lists['select_item_captcha']; ?>
						<div class="guests-only">
							<input id="guests-only" type="checkbox" name="params[captcha_guest_only]" <?php echo $this->submission->getParams()->get('captcha_guest_only', false) ? 'checked="checked"' : ''; ?> />
							<label for="guests-only"><?php echo JText::_('Guests only'); ?></label>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</fieldset>
		   <fieldset class="creation-form">
			   <legend><?php echo JText::_('Security'); ?></legend>
				<div class="element element-access-level">
					<strong><?php echo JText::_('Access level'); ?></strong>
					<?php echo $this->lists['select_access']; ?>
				</div>
				<div class="element element-max-submissions">
					<strong class="hasTip" title="<?php echo JText::_('Max Submissions per User'); ?>"><?php echo JText::_('Submissions Limit'); ?></strong>
					<input type="text" name="params[max_submissions]" value="<?php echo $this->submission->getParams()->get('max_submissions', '0'); ?>" />
				</div>
				<div class="element element-trusted-mode">
					<strong><?php echo JText::_('Trusted Mode'); ?></strong>
					<input id="trusted-mode" type="checkbox" name="params[trusted_mode]" class="trusted" <?php echo $this->submission->isInTrustedMode() ? 'checked="checked"' : ''; ?> />
					<label for="trusted-mode"><?php echo JText::_('TRUSTED_MODE_DESCRIPTION'); ?></label>
				</div>
		   </fieldset>
		   <fieldset>
				<legend><?php echo JText::_('Types'); ?></legend>
				<?php if (count($this->types)) : ?>
				<table class="admintable">
					<thead>
						<tr>
							<th class="type">
								<?php echo JText::_('Type'); ?>
							</th>
							<th class="layout">
								<?php echo JText::_('Layout'); ?>
							</th>
							<th class="category">
								<?php echo JText::_('SORT INTO CATEGORY ONLY IN NONE TRUSTED MODE'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($this->types as $type) : ?>
						<tr>
							<td class="name">
								<?php echo $type['name'];?>
							</td>
							<td class="layout">
								<?php echo $type['select_layouts'];?>
							</td>
							<td class="category">
								<?php echo $type['select_categories']?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php else: ?>
					<span class="no-types"><?php echo JText::_('No submission layouts available'); ?></span>
				<?php endif; ?>
		   </fieldset>

		</div>
		<div class="col col-right width-40">

			<div id="parameter-accordion">
				<?php $form = $this->application->getParamsForm()->setValues($this->submission->getParams()->get('content.')); ?>
				<?php if ($form->getParamsCount('submission-content')) : ?>
					<h3 class="toggler"><?php echo JText::_('Content'); ?></h3>
					<div class="content">
						<?php echo $form->render('params[content]', 'submission-content'); ?>
					</div>
				<?php endif; ?>
				<?php $form = $this->application->getParamsForm()->setValues($this->submission->getParams()->get('config.')); ?>
				<?php if ($form->getParamsCount('submission-config')) : ?>
					<h3 class="toggler"><?php echo JText::_('Config'); ?></h3>
					<div class="content">
						<?php echo $form->render('params[config]', 'submission-config'); ?>
					</div>
				<?php endif; ?>
			</div>

		</div>
	</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="cid[]" value="<?php echo $this->submission->id; ?>" />
<input type="hidden" name="changeapp" value="<?php echo $this->application->id; ?>" />
<?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($) {

		<?php
			$groups = array();
			foreach ($this->app->zoo->getGroups() as $group) {
				if (!JAccess::checkGroup($group->id, 'core.login.site')) {
					$groups[] = $group->id;
				}
			}
		?>

		$('#adminForm').EditSubmission({ groups: <?php echo json_encode($groups); ?> });
		$('#name-edit').AliasEdit({ edit: <?php echo (int) $this->submission->id; ?> });
		$('#name-edit').find('input[name="name"]').focus();
	});
</script>

<?php echo ZOO_COPYRIGHT;