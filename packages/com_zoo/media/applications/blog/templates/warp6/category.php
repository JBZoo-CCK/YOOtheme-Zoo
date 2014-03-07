<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets css/js
$this->app->document->addStylesheet($this->template->resource.'assets/css/style.css');

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

<div id="system" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-'.$this->category->alias; ?>">

	<?php if ($this->params->get('template.show_title')) : ?>
	<h3 class="page-title"><?php echo JText::_('Articles in Category').': '.$this->category->name; ?></h3>
	<?php endif; ?>
	
	<?php if ($this->category->getParams()->get('content.subtitle')) : ?>
	<h3 class="title"><?php echo $this->category->getParams()->get('content.subtitle') ?></h3>
	<?php endif; ?>

	<?php if ($this->params->get('template.show_description') || $this->params->get('template.show_image')) : ?>
	<div class="description">
		<?php if ($this->params->get('template.show_image')) : ?>
			<img class="size-auto align-<?php echo $this->params->get('template.alignment'); ?>" src="<?php echo $image['src']; ?>" <?php echo $image['width_height']; ?> title="<?php echo $this->category->name; ?>" alt="<?php echo $this->category->name; ?>" />
		<?php endif; ?>
		<?php if ($this->params->get('template.show_description')) echo $this->category->getText($this->category->description); ?>
	</div>
	<?php endif; ?>

	<?php

		// render items
		if (count($this->items)) {
			echo $this->partial('items');
		}
		
	?>

</div>