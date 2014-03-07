<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// get elements meta data
$name = isset($position, $index) ? 'positions['.$position.']['.$index.']' : 'elements['.$element->identifier.']';
$form = $element->getConfigForm();
$form->layout_path = $this->path;
$form->selectable_types = $element->config->get('selectable_types', array());

?>
<li class="element hideconfig" data-element="<?php echo $element->identifier; ?>">
	<div class="element-icon edit-element edit-event" title="<?php echo JText::_('Edit element'); ?>"></div>
	<div class="element-icon delete-element delete-event" title="<?php echo JText::_('Delete element'); ?>"></div>
	<div class="name sort-event" title="<?php echo JText::_('Drag to sort'); ?>"><?php echo $element->config->get('name'); ?>
	<?php if ($element->getGroup() != 'Core') :?>
		<span>(<?php echo $element->getMetaData('name'); ?>)</span>
	<?php endif;?>
	</div>
	<div class="config">
		<?php echo $form->setValues($data)->render($name, 'render'); ?>
		<input type="hidden" name="<?php echo $name;?>[element]" value="<?php echo $element->identifier; ?>" />
	</div>
</li>
