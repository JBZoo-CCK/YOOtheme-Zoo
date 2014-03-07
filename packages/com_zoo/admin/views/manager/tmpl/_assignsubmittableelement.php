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
<li class="element hideconfig" data-element="<?php echo $element->identifier; ?>">
	<div class="element-icon edit-element edit-event" title="<?php echo JText::_('Edit element'); ?>"></div>
	<div class="element-icon delete-element delete-event" title="<?php echo JText::_('Delete element'); ?>"></div>
	<div class="name sort-event" title="<?php echo JText::_('Drag to sort'); ?>"><?php echo $element->config->get('name'); ?>
		<span>(<?php echo $element->getMetaData('name'); ?>)</span>
	</div>
	<div class="config">
		<?php echo $element->getConfigForm()->setValues($data)->render($element->identifier, 'submission'); ?>
		<input type="hidden" name="<?php echo $element->identifier; ?>[element]" value="<?php echo $element->identifier; ?>" />
	</div>
</li>
