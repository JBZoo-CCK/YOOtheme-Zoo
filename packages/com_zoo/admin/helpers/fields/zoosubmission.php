<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

$this->app->html->_('behavior.modal', 'a.modal');
$this->app->document->addStylesheet('fields:zoosubmission.css');
$this->app->document->addScript('fields:zoosubmission.js');

// init vars
$params	= $this->app->parameterform->convertParams($parent);
$table  = $this->app->table->application;

$show_types = (boolean) $node->attributes()->types;

// create application/category select
$submissions = array();
$types       = array();
$app_options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Application').' -'));

foreach ($table->all(array('order' => 'name')) as $application) {
	// application option
	$app_options[$application->id] = $this->app->html->_('select.option', $application->id, $application->name);

	// create submission select
	$submission_options = array();
	foreach ($application->getSubmissions() as $submission) {
		$submission_options[$submission->id] = $this->app->html->_('select.option', $submission->id, $submission->name);

		if ($show_types) {
			$type_options = array();
			$type_objects = $submission->getSubmittableTypes();
			if (!count($type_objects)) {
				unset($submission_options[$submission->id]);
				continue;
			}

			foreach ($type_objects as $type) {
				$type_options[] = $this->app->html->_('select.option', $type->id, $type->name);
			}

			$attribs = 'data-type="type" data-submission="'.$submission->id.'" data-app="'.$application->id.'" data-control="'.$control_name.'[type]"';
			$types[] = $this->app->html->_('select.genericlist', $type_options, $control_name.'[type]', $attribs, 'value', 'text', $params->get('type'), 'submission-type-'.$submission->id);
		}
	}

	if (!count($submission_options)) {
		unset($app_options[$application->id]);
		continue;
	}

	$attribs = 'data-type="submission" data-app="'.$application->id.'" data-control="'.$control_name.'[submission]"';
	$submissions[] = $this->app->html->_('select.genericlist', $submission_options, $control_name.'[submission]', $attribs, 'value', 'text', $params->get('submission'), 'submission-'.$submission->id);
}

// create html
$html[] = '<div id="'.$name.'" class="zoo-submission">';

// create application html
$html[] = $this->app->html->_('select.genericlist', $app_options, $control_name.'['.$name.']', 'data-type="application"', 'value', 'text', $value);

// create submission html
$html[] = '<div class="submissions">'.implode("\n", $submissions).'</div>';

// create types html
if ($show_types) {
	$html[] = '<div class="types">'.implode("\n", $types).'</div>';
}

$html[] = '</div>';

$javascript  = 'jQuery(function($) { jQuery("#'.$name.'").ZooSubmission(); });';
$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";

echo implode("\n", $html).$javascript;