<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8" enctype="multipart/form-data">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="application-list">

		<h2><?php echo JText::_('SELECT_APP_CONFIGURE'); ?></h2>

		<?php foreach ($this->applications as $application) : ?>
			<a href="<?php echo $this->app->link(array('controller' => $this->controller, 'task' => 'types', 'group' => $application->getGroup())); ?>">
				<span>
					<img src="<?php echo $application->getIcon();?>" alt="<?php $application->getGroup(); ?>" />
					<?php echo $application->getMetaData('name'); ?>
				</span>
			</a>
		<?php endforeach; ?>
	</div>

	<div class="importbox uploadbox install">
		<div>
			<h3><?php echo JText::_('Install a new App'); ?></h3>
			<input type="text" id="filename" readonly="readonly" />
			<div class="button-container">
			  <button class="button-grey search" type="button"><?php echo JText::_('Search'); ?></button>
			  <input type="file" name="install_package" onchange="javascript: document.getElementById('filename').value = this.value.replace(/^.*[\/\\]/g, '');" />
			</div>
			<button class="button-green upload" type="button"><?php echo JText::_('Upload'); ?></button>
		</div>
	</div>

	<div class="importbox uploadbox backup">
		<div>
			<h3><?php echo JText::_('Restore Database Backup'); ?></h3>
			<input type="text" id="backupfile" readonly="readonly" />
			<div class="button-container">
			  <button class="button-grey search" type="button"><?php echo JText::_('Search'); ?></button>
			  <input type="file" name="backupfile" onchange="javascript: document.getElementById('backupfile').value = this.value" />
			</div>
			<button class="button-green upload" type="button"><?php echo JText::_('Upload'); ?></button>
		</div>
	</div>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="type" value="" />
<input type="hidden" name="installtype" value="upload" />
<?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($) {
		$('#adminForm div.install button.upload').bind('click', function () {
			if ($('#adminForm #filename').val() == '') {
				alert('<?php echo JText::_('SELECT_FILE_FIRST');?>');
			} else {
				submitbutton('installapplication');
			}
		});

		$('#adminForm div.backup button.upload').bind('click', function () {
			if ($('#adminForm #backupfile').val() == '') {
				alert('<?php echo JText::_('SELECT_FILE_FIRST');?>');
			} else {
				if (confirm('<?php echo JText::_('DATABASE_RESTORE_WARNING');?>')) {
					submitbutton('restorebackup');
				}
			}
		});
	});
</script>

<?php echo ZOO_COPYRIGHT;