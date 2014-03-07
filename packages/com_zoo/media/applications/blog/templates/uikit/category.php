<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// show description only if it has content
if (!$this->category->description) {
	$this->params->set('template.show_description', 0);
}

// show image only if an image is selected
if (!($image = $this->category->getImage('content.image'))) {
	$this->params->set('template.show_image', 0);
}

$css_class = $this->application->getGroup().'-'.$this->template->name;

?>

<div class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-'.$this->category->alias; ?>">

	<?php if ($this->params->get('template.show_title')) : ?>
	<h2 class="uk-h3 <?php echo 'uk-align-'.$this->params->get('template.alignment'); ?>"><?php echo JText::_('Articles in Category').': '.$this->category->name; ?></h2>
	<?php endif; ?>

	<?php if ($this->category->getParams()->get('content.subtitle')) : ?>
	<p class="uk-text-large <?php echo 'uk-text-'.$this->params->get('template.alignment'); ?>"><?php echo $this->category->getParams()->get('content.subtitle') ?></p>
	<?php endif; ?>

	<?php if ($this->params->get('template.show_description') || $this->params->get('template.show_image')) : ?>
	<div class="uk-margin">
		<?php if ($this->params->get('template.show_image')) : ?>
			<img class="<?php echo 'uk-align-'.($this->params->get('template.alignment') == "left" || $this->params->get('template.alignment') == "right" ? 'medium-' : '').$this->params->get('template.alignment'); ?>" src="<?php echo $image['src']; ?>" <?php echo $image['width_height']; ?> title="<?php echo $this->category->name; ?>" alt="<?php echo $this->category->name; ?>" />
		<?php endif; ?>
		<?php if ($this->params->get('template.show_description')) echo $this->category->getText($this->category->description); ?>
	</div>

	<hr>

	<?php endif; ?>

	<?php

		// render items
		if (count($this->items)) {
			echo $this->partial('items');
		}

	?>

</div>