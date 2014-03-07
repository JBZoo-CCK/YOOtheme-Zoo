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
$form = $element->getConfigForm();
$form->application = $this->application;
$name = $element->config->get('name', 'New');
$var = 'elements['.$element->identifier.']';

?>

<div class="element-icon edit-element edit-event" title="<?php echo JText::_('Edit element'); ?>"></div>
<div class="element-icon delete-element delete-event" title="<?php echo JText::_('Delete element'); ?>"></div>
<div class="name sort-event" title="<?php echo JText::_('Drag to sort'); ?>"><?php echo $name; ?> <span>(<?php echo $element->getMetaData('name'); ?>)</span></div>
<div class="config">
	<?php echo $form->render($var); ?>
	<input type="hidden" name="<?php echo $var; ?>[type]" value="<?php echo $element->getElementType(); ?>" />
</div>