<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<form class="menu-has-level3" action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8" enctype="multipart/form-data">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="col col-left width-60">

		<h2><?php echo JText::_('CSV Import'); ?>:</h2>
		<fieldset class="csv-details creation-form">
			<legend><?php echo JText::_('File Details:'); ?></legend>
			<div class="element element-contains-headers">
				<strong><?php echo JText::_('Contains Headers'); ?></strong>
				<input type="checkbox" name="contains-headers" checked="checked"></input>
			</div>
			<div class="element element-field-separator">
				<strong><?php echo JText::_('Field Separator'); ?></strong>
				<div class="row">
					<input type="text" name="field-separator" value=","></input>
				</div>
			</div>
			<div class="element element-field-enclosure">
				<strong><?php echo JText::_('Field Enclosure'); ?></strong>
				<div class="row">
					<input type="text" name="field-enclosure" value="&quot;"></input>
				</div>
			</div>
		</fieldset>

		<button class="button-grey" id="submit-button" type="submit"><?php echo JText::_('Next'); ?></button>

	</div>
</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="importcsv" />
<input type="hidden" name="file" value="<?php echo $this->file; ?>" />
<input type="hidden" name="changeapp" value="<?php echo $this->application->id; ?>" />
<?php echo $this->app->html->_('form.token'); ?>

</form>

<?php echo ZOO_COPYRIGHT;