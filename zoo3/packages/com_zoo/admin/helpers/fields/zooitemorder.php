<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

$this->app->document->addStylesheet('fields:zooitemorder.css');
$this->app->document->addScript('fields:zooitemorder.js');

// init vars
$params     = $this->app->parameterform->convertParams($parent);
$item_order = (array) $params->get((string) $name, array('_itemname'));

$html = array();
$html[] = '<div id="item-order" class="zoo-itemorder">';

	$html[] = '<span class="select-message">'.JText::_('Please select application first').'</span>';

	// add types
	$html[] = '<div class="apps">';

	if ($node->attributes('apps') || @$node->attributes()->apps) {
		$applications = $this->app->application->getApplications();
	} else if ($group = $this->app->request->getString('group', false)) {
		$applications = array($this->app->object->create('Application')->setGroup($group));
	} else {
		$applications = array($this->app->zoo->getApplication());
	}

	foreach ($applications as $application) {

		$types = $application->getTypes();

		// add core elements
		$core = $this->app->object->create('Type', array('_core', $application));
		$core->name = JText::_('Core');
		array_unshift($types, $core);

		$html[] = '<div class="app '.$application->id.'">';

			foreach ($types as $type) {

				if ($type->identifier == '_core') {
					$elements = $type->getCoreElements();
					$options = array();
				} else {
					$elements = $type->getElements();
					$options = array($this->app->html->_('select.option', false, '- '.JText::_('Select Element').' -'));
				}

				// filter orderable elements
				$elements = array_filter($elements, create_function('$element', 'return $element->getMetaData("orderable") == "true";'));

				$value = false;
				foreach ($elements as $element) {
					if (in_array($element->identifier, $item_order)) {
						$value = $element->identifier;
					}
					$options[] = $this->app->html->_('select.option', $element->identifier, ($element->config->name ? $element->config->name : $element->getMetaData('name')));
				}
				if ($type->identifier == '_core' && ($node->attributes('add_default') || @$node->attributes()->add_default)) {
					array_unshift($options, $this->app->html->_('select.option', '', JText::_('default')));
				}

				$id = $control_name.$name.$application->id.$type->identifier;
				$html[] = '<div class="type">';
				$html[] = $this->app->html->_('select.genericlist',  $options, "{$control_name}[{$name}][]", 'class="element"', 'value', 'text', $value, $id);
				$html[] = '<label for="'.$id.'">' . $type->name . '</label>';
				$html[] = '</div>';
				if ($type->identifier == '_core') {
					$html[] = '<span>- '.JText::_('JOR').' -</span>';
				}
			}

		$html[] = '</div>';
	}
	$html[] = '</div>';
	$html[] = '<div>';
		$id = "{$control_name}[{$name}][_reversed]";
		$html[] = "<input type=\"checkbox\" id=\"{$id}\" name=\"{$control_name}[{$name}][]\"" . (in_array('_reversed', $item_order) ? 'checked="checked"' : '') . ' value="_reversed" />';
		$html[] = '<label for="'.$id.'">' . JText::_('Reverse') . '</label>';
	$html[] = '</div>';

	if ($node->attributes('random') || @$node->attributes()->random) {
		$html[] = '<div>';
			$id = "{$control_name}[{$name}][_random]";
			$html[] = "<input type=\"checkbox\" id=\"{$id}\" name=\"{$control_name}[{$name}][]\"" . (in_array('_random', $item_order) ? 'checked="checked"' : '') . ' value="_random" />';
			$html[] = '<label for="'.$id.'">' . JText::_('Random') . '</label>';
		$html[] = '</div>';
	}

	$html[] = '<div>';
		$id = "{$control_name}[{$name}][_alphanumeric]";
		$html[] = "<input type=\"checkbox\" id=\"{$id}\" name=\"{$control_name}[{$name}][]\"" . (in_array('_alphanumeric', $item_order) ? 'checked="checked"' : '') . ' value="_alphanumeric" />';
		$html[] = '<label for="'.$id.'">' . JText::_('Alphanumeric sorting') . '</label>';
	$html[] = '</div>';

$html[] = '</div>';
$html[] = "<script type=\"text/javascript\">\n// <!--\njQuery('#item-order').ZooItemOrder();\n// -->\n</script>\n";

echo implode("\n", $html);