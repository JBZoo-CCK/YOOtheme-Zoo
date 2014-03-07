<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/
	defined('_JEXEC') or die('Restricted access');
	JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

	<?php echo $this->partial('menu'); ?>

	<div class="box-bottom">

		<table class="admintable" width="100%">
			<tr valign="top">
				<td width="60%">
					<!-- Menu Type Section -->
					<fieldset>
						<legend><?php echo JText::_('Select Item Type'); ?></legend>
						<ul id="item-type" class="jtree">
							<?php foreach ($this->types as $type) : ?>
								<li><div class="node-open"><span></span><a href="<?php echo JRoute::_($this->baseurl.'&task=edit&type='. $type->id); ?>"><?php echo $type->name; ?></a></div>
							</li>
							<?php endforeach; ?>
						</ul>
					</fieldset>
				</td>
				<td width="40%">
				</td>
			</tr>
		</table>

	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="changeapp" value="<?php echo $this->application->id; ?>" />
	<?php echo $this->app->html->_('form.token'); ?>
</form>

<?php echo ZOO_COPYRIGHT;