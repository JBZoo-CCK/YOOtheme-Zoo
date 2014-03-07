<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// add js
$this->app->document->addScript('assets:js/update.js');

?>

<form class="update-default" action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

	<div class="box-bottom">

		<?php if ($this->update) :?>

		<div class="col col-left width-40">
			<div class="updatebox">
				<div>
					<h3><?php echo JText::_('ZOO requires to be updated:'); ?></h3>
					<button class="button-green update" type="button">
						<span><?php echo JText::_('Start Update'); ?></span>
					</button>
				</div>
				<div class="message-box"></div>
			</div>

		</div>

		<div class="col col-right width-60">
			<h2><?php echo JText::_('Information'); ?>:</h2>

			<div class="creation-form wrapper">
				<p><?php echo JText::_("For the ZOO to function correctly it needs to run the following update script(s):"); ?></p>
				<ul>
					<?php foreach ($this->app->update->getRequiredUpdates() as $update) : ?>
					<li><strong><?php echo preg_replace('/\.beta[\d]*/', '', $update); ?></strong></li>
					<?php endforeach; ?>

				</ul>
			</div>

			<?php if (!empty($this->notifications)) : ?>
				<div class="notifications creation-form wrapper">
					<p><strong><?php echo JText::_("Important Update Notifications:"); ?></strong></p>
					<ul>
						<?php foreach ($this->notifications as $notification) : ?>
							<li><?php echo $notification; ?></li>
						<?php endforeach; ?>

					</ul>
				</div>
			<?php endif; ?>

			<div class="wrapper changelog">
                <?php $text = file_get_contents($this->app->path->path('component.admin:README.markdown')); ?>
				<textarea disabled="disabled" rows="20" cols="75" name="changelog"><?php echo JFilterOutput::cleanText($text); ?></textarea>
			</div>

		</div>

		<?php else :

				$title   = JText::_('No further Update required').'!';
				$message = null;
				echo $this->partial('message', compact('title', 'message'));

			endif;
		?>

	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="step" />
	<input type="hidden" name="format" value="raw" />
	<?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($) {
		$('#adminForm').Update({
			msgPerformingUpdate: '<?php echo JText::_('Performing Update...'); ?>',
			msgFinished: '<?php echo JText::_('Update successfull...Reload page to continue working.'); ?>',
			msgError: '<?php echo JText::_('Error during update. Please visit the YOOtheme support forums.'); ?>'
		});
	});
</script>

<?php echo ZOO_COPYRIGHT;