<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/
	defined('_JEXEC') or die('Restricted access');

	$this->app->html->_('behavior.tooltip');

	// Add chosen in Joomla 2.5
	if ($this->app->joomla->isVersion('2.5')) {
		$this->app->document->addScript('libraries:jquery/plugins/chosen/chosen.jquery.min.js');
		$this->app->document->addStylesheet('libraries:jquery/plugins/chosen/chosen.css');
	} else {
		JHtml::_('formbehavior.chosen', '#parent');
	}

	// add script
	$this->app->document->addScript('assets:js/alias.js');
	$this->app->document->addScript('assets:js/category.js');

	// filter output
	JFilterOutput::objectHTMLSafe($this->category, ENT_QUOTES, array('params'));

	// Keepalive behavior
	JHTML::_('behavior.keepalive');
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
						<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->category->name; ?>" />
						<span class="message-name"><?php echo JText::_('Please enter valid name.'); ?></span>
					</div>
					<div class="slug">
						<span><?php echo JText::_('Slug'); ?>:</span>
						<a class="trigger" href="#" title="<?php echo JText::_('Edit Category Slug');?>"><?php echo $this->category->alias; ?></a>
						<div class="panel">
							<input type="text" name="alias" value="<?php echo $this->category->alias; ?>" />
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
			<div class="element element-parent-item">
				<strong><?php echo JText::_('Parent Category'); ?></strong>
				<?php echo $this->lists['select_parent']; ?>
			</div>
		<div class="element element-description">
			<strong><?php echo JText::_('Description'); ?></strong>
			<div>
				<?php
					// parameters : areaname, content, width, height, cols, rows, show xtd buttons
					echo $this->app->system->editor->display('description', $this->category->description, null, null, '60', '20', array('pagebreak', 'readmore', 'article')) ;
				?>
			</div>
		</div>
		</fieldset>

	</div>

	<div class="col col-right width-40">

		<div id="parameter-accordion">
			<?php $form = $this->application->getParamsForm()->setValues($this->params->get('content.')); ?>
			<?php if ($form->getParamsCount('category-content')) : ?>
			<h3 class="toggler"><?php echo JText::_('Content'); ?></h3>
			<div class="content">
				<?php echo $this->application->getParamsForm()->setValues($this->params->get('content.'))->render('params[content]', 'category-content'); ?>
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
			<?php $form = $this->app->parameterform->create(dirname(__FILE__).'/params.xml'); ?>
			<h3 class="toggler"><?php echo JText::_('Metadata'); ?></h3>
			<div class="content">
				<?php echo $form->setValues($this->params->get('metadata.'))->render('params[metadata]', 'metadata'); ?>
			</div>
		</div>

	</div>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
<input type="hidden" name="cid[]" value="<?php echo $this->category->id; ?>" />
<input type="hidden" name="changeapp" value="<?php echo $this->application->id; ?>" />
<?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($) {
		$('#adminForm').EditCategory();
		$('#name-edit').AliasEdit({ edit: <?php echo (int) $this->category->id; ?> });
		$('#name-edit').find('input[name="name"]').focus();

		<?php if ($this->app->joomla->isVersion('2.5')) : ?>
		$('#parent').chosen({ disable_search_threshold : 10, allow_single_deselect : true });
		<?php endif; ?>

		// Add here since on 3.0 the options are hardcoded in the constructor of the PHP method
		$('#parent').data('chosen').search_contains = true;
	});
</script>

<?php echo ZOO_COPYRIGHT;