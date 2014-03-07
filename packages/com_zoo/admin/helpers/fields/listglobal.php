<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// load js
$this->app->document->addScript('fields:global.js');

// init vars
$id      = uniqid('listglobal-');
$global  = $parent->getValue((string) $name) === null;

?>

<div class="global list">
	<?php echo '<input id="'.$id.'" type="checkbox"'.($global ? ' checked="checked"' : '').' />'; ?>
	<?php echo '<label for="'.$id.'">'.JText::_('Global').'</label>'; ?>
	<div class="input">
		<?php echo $this->app->field->render('list', $name, $value, $node, compact('control_name', 'parent')); ?>
	</div>
</div>