<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

	<?php echo $this->partial('menu'); ?>

	<div class="box-bottom">

		<div class="application-list">

			<h2><?php echo JText::_('SELECT_APP_TO_CREATE_INSTANCE'); ?></h2>

			<?php foreach ($this->applications as $application) : ?>
				<a href="<?php echo $this->app->link(array('controller' => $this->controller, 'task' => 'add', 'group' => $application->getGroup())); ?>">
					<span>
						<img src="<?php echo $application->getIcon(); ?>" alt="<?php echo $application->getGroup(); ?>" />
						<?php echo $application->getMetaData('name'); ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>

	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo $this->app->html->_('form.token'); ?>

</form>

<?php echo ZOO_COPYRIGHT;