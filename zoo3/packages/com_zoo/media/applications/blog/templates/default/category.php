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
if (strtolower(substr($GLOBALS['app']->getTemplate(), 0, 3)) != 'yoo') {
	$this->app->document->addStylesheet('assets:css/reset.css');
}
$this->app->document->addStylesheet($this->template->resource.'assets/css/zoo.css');

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

<div id="yoo-zoo" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-'.$this->category->alias; ?>">

	<?php if ($this->params->get('template.show_title') || $this->params->get('template.show_description') || $this->params->get('template.show_image')) : ?>
	<div class="details <?php echo 'alignment-'.$this->params->get('template.alignment'); ?>">

		<?php if ($this->params->get('template.show_title') || $this->category->getParams()->get('template.subtitle')) : ?>
		<div class="heading">

			<?php if ($this->params->get('template.show_title')) : ?>
			<h1 class="title"><?php echo $this->category->name; ?></h1>
			<?php endif; ?>

			<?php if ($this->category->getParams()->get('content.subtitle')) : ?>
			<h2 class="subtitle">
				<?php echo $this->category->getParams()->get('content.subtitle') ?>
			</h2>
			<?php endif; ?>

		</div>
		<?php endif; ?>

		<?php if ($this->params->get('template.show_description') || $this->params->get('template.show_image')) : ?>
		<div class="description">
			<?php if ($this->params->get('template.show_image')) : ?>
					<img class="image" src="<?php echo $image['src']; ?>" title="<?php echo $this->category->name; ?>" alt="<?php echo $this->category->name; ?>" <?php echo $image['width_height']; ?>/>
				<?php endif; ?>
			<?php if ($this->params->get('template.show_description')) echo $this->category->getText($this->category->description); ?>
		</div>
		<?php endif; ?>

	</div>
	<?php endif; ?>

	<?php

		// render items
		if (count($this->items)) {
			echo $this->partial('items');
		}

	?>

</div>