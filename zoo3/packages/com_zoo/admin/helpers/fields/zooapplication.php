<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// load zoo frontend language file
$this->app->system->language->load('com_zoo');

$this->app->html->_('behavior.modal', 'a.modal');
$this->app->document->addStylesheet('fields:zooapplication.css');
$this->app->document->addScript('fields:zooapplication.js');

// init vars
$params	= $this->app->parameterform->convertParams($parent);
$table	= $this->app->table->application;

// set modes
$modes = array();

if ($node->attributes()->allitems) {
	$modes[] = $this->app->html->_('select.option', 'all', JText::_('All Items'));
}

if ($node->attributes()->categories) {
	$modes[] = $this->app->html->_('select.option', 'categories', JText::_('Categories'));
}

if ($node->attributes()->types) {
	$modes[] = $this->app->html->_('select.option', 'types', JText::_('Types'));
}

if ($node->attributes()->items) {
	$modes[] = $this->app->html->_('select.option', 'item', JText::_('Item'));
}

// create application/category select
$cats    = array();
$types   = array();
$options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Application').' -'));

foreach ($table->all(array('order' => 'name')) as $application) {

	// application option
	$options[] = $this->app->html->_('select.option', $application->id, $application->name);

	// create category select
	if ($node->attributes()->categories) {
		$attribs = 'class="category app-'.$application->id.($value != $application->id ? ' hidden' : null).'" data-category="'.$control_name.'[category]"';
		$opts = array();
		if ($node->attributes()->frontpage) {
			$opts[] = $this->app->html->_('select.option', '', '&#8226;	'.JText::_('Frontpage'));
		}
		$cats[]  = $this->app->html->_('zoo.categorylist', $application, $opts, ($value == $application->id ? $control_name.'[category]' : null), $attribs, 'value', 'text', $params->get('category'));
	}

	// create types select
	if ($node->attributes()->types) {
		$opts = array();

		foreach ($application->getTypes() as $type) {
			$opts[] = $this->app->html->_('select.option', $type->id, $type->name);
		}

		$attribs = 'class="type app-'.$application->id.($value != $application->id ? ' hidden' : null).'" data-type="'.$control_name.'[type]"';
		$types[] = $this->app->html->_('select.genericlist', $opts, $control_name.'[type]', $attribs, 'value', 'text', $params->get('type'), 'application-type-'.$application->id);
	}
}

// create html
$html[] = '<div id="'.$name.'" class="zoo-application">';
$html[] = $this->app->html->_('select.genericlist', $options, $control_name.'['.$name.']', 'class="application"', 'value', 'text', $value);

// create mode select
if (count($modes) > 1) {
	$html[] = $this->app->html->_('select.genericlist', $modes, $control_name.'[mode]', 'class="mode"', 'value', 'text', $params->get('mode'));
}

// create categories html
if (!empty($cats)) {
	$html[] = '<div class="categories">'.implode("\n", $cats).'</div>';
}

// create types html
if (!empty($types)) {
	$html[] = '<div class="types">'.implode("\n", $types).'</div>';
}

// create items html
$link = '';
if ($node->attributes()->items) {

	$field_name	= $control_name.'[item_id]';
	$item_name  = JText::_('Select Item');

	if ($item_id = $params->get('item_id')) {
		$item = $this->app->table->item->get($item_id);
		$item_name = $item->name;
	}

	$link = $this->app->link(array('controller' => 'item', 'task' => 'element', 'tmpl' => 'component', 'func' => 'selectZooItem', 'object' => $name), false);

	$html[] = '<div class="item">';
	$html[] = '<input type="text" id="'.$name.'_name" value="'.htmlspecialchars($item_name, ENT_QUOTES, 'UTF-8').'" disabled="disabled" />';
	$html[] = '<a class="modal" title="'.JText::_('Select Item').'"  href="#" rel="{handler: \'iframe\', size: {x: 850, y: 500}}">'.JText::_('Select').'</a>';
	$html[] = '<input type="hidden" id="'.$name.'_id" name="'.$field_name.'" value="'.(int) $item_id.'" />';
	$html[] = '</div>';

}

$html[] = '</div>';

$javascript  = 'jQuery(function($) { jQuery("#'.$name.'").ZooApplication({ url: "'.$link.'", msgSelectItem: "'.JText::_('Select Item').'" }); });';
$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";

echo implode("\n", $html).$javascript;
