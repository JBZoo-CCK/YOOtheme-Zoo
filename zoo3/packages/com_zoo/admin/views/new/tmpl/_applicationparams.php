<?php defined('_JEXEC') or die('Restricted access'); ?>

<h3 class="toggler"><?php echo JText::_('CONFIGURATION GLOBAL'); ?></h3>
<div class="content">
	<?php echo $this->application->getParamsForm()->setValues($this->params->get('global.config.'))->render('params[config]', 'application-config'); ?>
</div>

<h3 class="toggler"><?php echo JText::_('TEMPLATE GLOBAL'); ?></h3>
<div class="content">
	<?php
		if ($template = $this->application->getTemplate()) {
			if ($params_form = $template->getParamsForm()) {
				echo $params_form->setValues($this->params->get('global.template.'))->render('params[template]', 'category');
				echo $params_form->setValues($this->params->get('global.template.'))->render('params[template]', 'item');
			}
		} else {
			echo '<em>'.JText::_('Please select a Template').'</em>';
		}
	?>
</div>

<?php foreach ($this->application->getAddonParamsForms() as $name => $params_form) : ?>
<h3 class="toggler"><?php echo JText::_($name); ?></h3>
<div class="content">
	<?php
		echo $params_form->setValues($this->params->get('global.'.strtolower($name).'.'))->render('addons['.strtolower($name).']');
	?>
</div>
<?php endforeach;