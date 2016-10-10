<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->document->addStylesheet($this->template->resource.'assets/css/submission.css');
$this->app->document->addScript('assets:js/submission.js');

$this->pagination_link = $this->app->route->mysubmissions($this->submission);

?>

<div id="mysubmissions">

	<div class="uk-clearfix">

		<div class="uk-align-left@m">

			<?php if($this->show_add): ?>
			<div class="uk-button-dropdown">
				<a class="uk-button uk-button-default trigger" href="javascript:void(0);" title="<?php echo JText::_('Add Item'); ?>"><?php echo JText::_('Add Item'); ?> <span uk-icon="icon:triangle-down"></span></a>
				<div uk-dropdown>
					<ul class="uk-nav uk-dropdown-nav">
					<?php foreach($this->types as $id => $type) : ?>
						<li>
							<?php $add_link = $this->app->route->submission($this->submission, $id, null, 0, 'mysubmissions'); ?>
							<a href="<?php echo JRoute::_($add_link); ?>" title="<?php echo sprintf(JText::_('Add %s'), $type->name); ?>"><span uk-icon="icon:plus"></span> <?php echo $type->name; ?></a>
						</li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php endif; ?>

		</div>

		<div class="uk-align-right@m">

			<?php if (isset($this->lists['select_type'])) : ?>
			<form action="<?php echo JRoute::_($this->pagination->link($this->pagination_link, 'page='.$this->page)); ?>" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">
				<?php echo $this->lists['select_type']; ?>
				<input class="uk-input" type="text" name="search" id="zoo-search" value="<?php echo $this->lists['search'];?>" />
				<button class="uk-button uk-button-default" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
				<button class="uk-button uk-button-default" onclick="document.getElementById('zoo-search').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
			</form>
			<?php endif; ?>

		</div>

	</div>

	<?php if (count($this->items)) : ?>
	<div class="submissions">

		<?php foreach ($this->items as $id => $item) : ?>
		<div class="zo-item">

			<div class="uk-card uk-card-default zo-header">
				<?php if ($this->submission->isInTrustedMode()) : ?>
					<a href="<?php echo $this->app->link(array('controller' => 'submission', 'submission_id' => $this->submission->id, 'task' => 'remove', 'item_id' => $id)); ?>" title="<?php echo JText::_('Delete Item'); ?>" class="delete-item"><span uk-icon="icon:close"></span></a>
				<?php endif; ?>
				<?php $edit_link = $this->app->route->submission($this->submission, $item->type, null, $id, 'mysubmissions'); ?>
				<a href="<?php echo JRoute::_($edit_link); ?>" title="<?php echo JText::_('Edit Item'); ?>"><span uk-icon="icon:file-edit"></span></a>
				<h3 class="uk-h5 toggler"><?php echo $item->name; ?> (<?php echo $item->getType()->name; ?>)</h3>
			 </div>

			<?php $this->params = $item->getParams('site'); ?>
			<?php $type = ($this->renderer->pathExists('item/'.$item->type)) ? $item->type : 'item'; ?>
			<div class="preview hidden <?php echo $type; ?>">
				<?php
					$layout  = 'item.'.($type != 'item' ? $item->type . '.' : '');
					echo $this->renderer->render($layout.'full', array('view' => $this, 'item' => $item));
				?>
			</div>

		</div>

	<?php endforeach; ?>
	</div>

	<?php else : ?>

		<?php if (empty($this->lists['search'])) : ?>
		<div class="uk-alert uk-alert-primary"><?php echo sprintf(JText::_('You have not submitted any %s items yet.'), $this->filter_type); ?></div>
		<?php else : ?>
		<div class="uk-alert uk-alert-primary"><?php echo JText::_('SEARCH_NO_ITEMS').'!'; ?></div>
		<?php endif; ?>

	<?php endif; ?>

	<ul class="uk-pagination">
		<?php echo $this->partial('pagination'); ?>
	</ul>

</div>

<script type="text/javascript">
	jQuery(function($) {
		$('#mysubmissions').SubmissionMysubmissions({ msgDelete: '<?php echo JText::_('SUBMISSION_DELETE_CONFIRMATION'); ?>' });
	});
</script>
