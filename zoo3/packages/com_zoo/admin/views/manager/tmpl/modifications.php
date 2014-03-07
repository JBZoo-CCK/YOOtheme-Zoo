<?php defined('_JEXEC') or die('Restricted access'); ?>

<style type="text/css">

	div.importbox {	margin-bottom: 15px; }

	.warning { color: #FF0000; }

</style>

<div class="creation-form">
	<h2><?php echo JText::_('Zoo Modifications'); ?></h2>

	<?php if (empty($this->results)) : ?>
		<div class="infobox"><?php echo JText::_('No modifications found!'); ?></div>
		<button class="button-green close" type="button"><?php echo JText::_('Close Window'); ?></button>
	<?php else: ?>
		<div class="infobox"><?php echo JText::_('This list will show you any changes to files in the ZOO front and backend. It will <strong>not</strong> show changes made to the applications in the <em>media</em> folder.'); ?></div>
	<?php endif; ?>

	<?php if (isset($this->results['missing'])) : ?>
		<div class="importbox">
			<div>
				<h3><?php echo JText::_('Missing Files'); ?>:</h3>
				<ul class="missing">
					<?php foreach($this->results['missing'] as $file) : ?>
						<li><?php echo $file; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php if (isset($this->results['modified'])) : ?>
		<div class="importbox">
			<div>
				<h3><?php echo JText::_('Modified Files'); ?>:</h3>
				<ul class="modified">
					<?php foreach($this->results['modified'] as $file) : ?>
						<li><?php echo $file; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php if (isset($this->results['unknown'])) : ?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8" enctype="multipart/form-data">
			<div class="importbox">
				<div>
					<h3><?php echo JText::_('Unknown Files'); ?>:</h3>
					<ul class="unknown">
						<?php foreach($this->results['unknown'] as $file) : ?>
							<li><?php echo $file; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<div class="infobox"><?php echo JText::_('If you have just upgraded the ZOO, those unknown files might be remnants of a previous ZOO installation.'); ?></div>
			<div class="infobox warning"><?php echo JText::_('<strong>Attention!</strong> Cleaning the ZOO will remove <strong>all</strong> unknown files. Modified files will not be touched.'); ?></div>
			<button class="button-green clean" type="submit"><?php echo JText::_('Clean'); ?></button>

			<input type="hidden" name="option" value="<?php echo $this->app->system->application->scope; ?>" />
			<input type="hidden" name="controller" value="manager" />
			<input type="hidden" name="task" value="cleanmodifications" />
			<input type="hidden" name="tmpl" value="component" />
			<?php echo $this->app->html->_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>

<script type="text/javascript">
	jQuery(function($) {
		$('button.clean').bind('click', function () {
			return confirm('<?php echo JText::_('This will remove all unknown files!'); ?>');
		});

		$('button.close').bind('click', function (event) {
			if (window.parent.document.getElementById('sbox-window').close) {
				window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 300);
			} else {
				window.top.setTimeout('window.parent.SqueezeBox.close()', 300);
			}
		});

	});
</script>