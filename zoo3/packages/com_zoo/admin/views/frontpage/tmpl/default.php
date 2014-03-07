<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');

$this->app->html->_('behavior.tooltip');

// filter output
JFilterOutput::objectHTMLSafe($this->application, ENT_QUOTES, array('params'));

?>

<form action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="col col-left width-60">

		<fieldset class="creation-form">
		<legend><?php echo JText::_('Details'); ?></legend>
		<div class="element element-description">
			<strong><?php echo JText::_('Description'); ?></strong>
			<div>
				<?php
					// parameters : areaname, content, width, height, cols, rows, show xtd buttons
					echo $this->app->system->editor->display('description', $this->application->description, null, null, '60', '20', array('pagebreak', 'readmore', 'article'));
				?>
			</div>
		</div>
		</fieldset>

	</div>

	<div class="col col-right width-40">

		<div id="parameter-accordion">
			<?php $form = $this->application->getParamsForm()->setValues($this->params->get('content.')); ?>
			<?php if ($form->getParamsCount('application-content')) : ?>
			<h3 class="toggler"><?php echo JText::_('Content'); ?></h3>
			<div class="content">
				<?php echo $form->render('params[content]', 'application-content'); ?>
			</div>
			<?php endif; ?>
			<?php $form = $this->application->getParamsForm()->setValues($this->params->get('config.')); ?>
			<?php if ($form->getParamsCount('category-config')) : ?>
			<h3 class="toggler"><?php echo JText::_('Config'); ?></h3>
			<div class="content">
				<?php echo $this->application->getParamsForm()->setValues($this->params->get('config.'))->render('params[config]', 'category-config'); ?>
			</div>
			<?php endif; ?>
			<?php $template = $this->application->getTemplate(); ?>
			<?php if ($template) : ?>
				<?php $form = $template->getParamsForm(true)->setValues($this->params->get('template.')); ?>
				<?php if ($form->getParamsCount('category')) : ?>
				<h3 class="toggler"><?php echo JText::_('Template'); ?></h3>
				<div class="content">
					<?php echo $form->render('params[template]', 'category'); ?>
				</div>
				<?php endif; ?>
			<?php else: ?>
				<h3 class="toggler"><?php echo JText::_('Template'); ?></h3>
				<div class="content">
					<em><?php echo JText::_('Please select a Template'); ?></em>
				</div>
			<?php endif; ?>
		</div>

	</div>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="changeapp" value="<?php echo $this->application->id; ?>" />
<?php echo $this->app->html->_('form.token'); ?>

</form>

<?php echo ZOO_COPYRIGHT;