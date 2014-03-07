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
$this->app->document->addScript('assets:js/import.js');

?>

<form class="configuration-import menu-has-level3" action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8" enctype="multipart/form-data">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="col col-left width-60">

		<h2><?php echo JText::_('CSV Import'); ?>:</h2>
		<fieldset class="items">
			<legend><?php echo (int) $this->info['item_count']; ?> x <?php echo JText::_('Items'); ?></legend>
			<div class="assign-group">

				<div class="info">
					<label for="type-select"><?php echo JText::_('CHOOSE_TYPE_MATCH_DATA'); ?></label>
					<?php
						$options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Type').' -'));
						echo $this->app->html->_('zoo.typelist', $this->application, $options, 'type', 'class="type"', 'value', 'text');
					?>
				</div>

				<ul>
				<?php foreach ($this->info['columns'] as $key => $column) : ?>
					<li class="assign">
						<?php
							foreach ($this->info['types'] as $type => $element_types) {
								$options = array();
								$options[] = $this->app->html->_('select.option', '', JText::_('Ignore'));
								$options[] = $this->app->html->_('select.option',  '<OPTGROUP>', JText::_('Core Atributes') );
								$options[] = $this->app->html->_('select.option', '_id', JText::_('Id'));
								$options[] = $this->app->html->_('select.option', '_name', JText::_('Name'));
								$options[] = $this->app->html->_('select.option', '_alias', JText::_('Alias'));
								$options[] = $this->app->html->_('select.option', '_category', JText::_('Category'));
								$options[] = $this->app->html->_('select.option', '_created_by_alias', JText::_('Author Alias'));
								$options[] = $this->app->html->_('select.option', '_created', JText::_('Created Date'));
								$options[] = $this->app->html->_('select.option', '_tag', JText::_('Tag'));
								$options[] = $this->app->html->_('select.option',  '</OPTGROUP>' );

								$options[] = $this->app->html->_('select.option',  '<OPTGROUP>', JText::_('Elements') );
								foreach ($element_types as $elements) {
									foreach ($elements as $element) {
										$options[] = $this->app->html->_('select.option', $element->identifier, $element->config->get('name') . ' (' . ucfirst($element->getElementType()) . ')');
									}
								}
								$options[] = $this->app->html->_('select.option',  '</OPTGROUP>' );
								echo $this->app->html->_('select.genericlist', $options, 'element-assign['.$key.']['.$type.']', 'class="assign"');
							}
						?>
						<span class="name"><?php echo empty($column) ? JText::_('Column') . ' ' . ($key + 1) : $column; ?></span>
					</li>
				<?php endforeach; ?>
				</ul>

			</div>
		</fieldset>

		<button class="button-grey" id="submit-button" type="button"><?php echo JText::_('Import'); ?></button>

	</div>

	<h2><?php echo JText::_('Information'); ?>:</h2>
	<div class="col col-right width-40">
		<div class="creation-form infobox">
			<p><?php echo JText::_("CSV-IMPORT-INFO-1"); ?></p>
			<p><?php echo JText::_("CSV-IMPORT-INFO-2"); ?></p>
			<p><?php echo JText::_("CSV-IMPORT-INFO-3"); ?></p>
		</div>
	</div>
</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="contains-headers" value="<?php echo $this->contains_headers; ?>" />
<input type="hidden" name="field-separator" value="<?php echo htmlentities($this->field_separator); ?>" />
<input type="hidden" name="field-enclosure" value="<?php echo htmlentities($this->field_enclosure); ?>" />
<input type="hidden" name="file" value="<?php echo $this->file; ?>" />
<input type="hidden" name="changeapp" value="<?php echo $this->application->id; ?>" />
<?php echo $this->app->html->_('form.token'); ?>

<script type="text/javascript">
	jQuery(function($) {
		$('#adminForm').Import({ msgNameWarning: "<?php echo JText::_("Please choose a name column."); ?>", msgSelectWarning: "<?php echo JText::_("MSG_ASSIGN_WARNING"); ?>", msgWarningDuplicate: "<?php echo JText::_("There are duplicate assignments."); ?>", task: "doimportcsv" });
	});
</script>

</form>

<?php echo ZOO_COPYRIGHT;