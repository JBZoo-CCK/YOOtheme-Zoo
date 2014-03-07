<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// Initialize variables.
$html = array();
$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field='.$name;

// Initialize some field attributes.
$attr = (string) $node->attributes()->class ? ' class="'.(string) $node->attributes()->class.'"' : '';
$attr .= (string) $node->attributes()->size ? ' size="'.(int) $node->attributes()->size.'"' : '';

// Initialize JavaScript field attributes.
$onchange = (string) $node->attributes()->onchange;

// Load the modal behavior script.
$this->app->html->_('behavior.modal', 'a.modal_'.$name);

// Build the script.
$script = array();
$script[] = '	function jSelectUser_'.$name.'(id, title) {';
$script[] = '		var old_id = document.getElementById("'.$name.'_id").value;';
$script[] = '		if (old_id != id) {';
$script[] = '			document.getElementById("'.$name.'_id").value = id;';
$script[] = '			document.getElementById("'.$name.'_name").value = title;';
$script[] = '			'.$onchange;
$script[] = '		}';
$script[] = '		SqueezeBox.close();';
$script[] = '	}';

// Add the script to the document head.
JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

// Load the current username if available.
$username = $value == 'NO_CHANGE' ? JText::_( 'No Change' ) : (($user = $this->app->user->get($value)) && $user->id ? $user->name : JText::_('JLIB_FORM_SELECT_USER'));

// Create a dummy text field with the user name.
$html[] = '<div class="fltlft">';
$html[] = '	<input type="text" id="'.$name.'_name"' .
			' value="'.htmlspecialchars($username, ENT_COMPAT, 'UTF-8').'"' .
			' disabled="disabled"'.$attr.' />';
$html[] = '</div>';

// Create the user select button.
$html[] = '<div class="button2-left">';
$html[] = '  <div class="blank">';
if ((string) $node->attributes()->readonly != 'true') {
	$html[] = '		<a class="modal_'.$name.'" title="'.JText::_('JLIB_FORM_CHANGE_USER').'"' .
					' href="'.$link.'"' .
					' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
	$html[] = '			'.JText::_('JLIB_FORM_CHANGE_USER').'</a>';
}
$html[] = '  </div>';
$html[] = '</div>';

// Create the real field, hidden, that stored the user id.
$html[] = '<input type="hidden" id="'.$name.'_id" name="'.$name.'" value="'.(int) $value.'" />';

echo implode("\n", $html);