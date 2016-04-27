<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$target = $target ? 'target="_blank"' : '';
$rel	= $rel ? 'data-lightbox="' . $rel .'"' : '';
$title  = $title ? ' title="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'"' : '';

if (!$params->get('link_to_item', false) && !$this->get('link') && $this->get('lightbox_image')) {
    $url = JURI::root() . $this->app->path->relative($this->app->zoo->resizeImage(JPATH_ROOT.'/'.$this->get('lightbox_image', ''), 0 , 0));
}

$link_enabled = !empty($url) && !$this->app->request->getBool('print', 0);
$lightbox = !$params->get('link_to_item') && $this->get('lightbox_image') && $link_enabled;

$overlay = $spotlight = '';
if ($this->get('spotlight_effect', false)) {
	if ($this->get('spotlight_effect') != 'default') {
		$caption = $this->get('caption') ? $this->get('caption') : basename($this->get('file'));
		$spotlight = 'data-spotlight="effect:'.$this->get('spotlight_effect').';"';
		$overlay = '<div class="overlay">'.$caption.'</div>';
	} else {
		$spotlight = 'data-spotlight="on"';
	}
}

$info = getimagesize($file);
$content = '<img src="'.$link.'"'.$title.' alt="'.$alt.'" '.$info[3].' />'.$overlay;

// init lightbox
if ($lightbox) {
	$this->app->document->addScript('assets:js/lightbox.js');
	$this->app->document->addStylesheet('assets:css/lightbox.css');
	$this->app->document->addScriptDeclaration("jQuery(function($) { $('[data-lightbox]').lightbox(); });");
}

// init spotlight
if ($spotlight) {
	$this->app->document->addScript('assets:js/spotlight.js');
	$this->app->document->addStylesheet('assets:css/spotlight.css');
	$this->app->document->addScriptDeclaration("jQuery(function($) { $('[data-spotlight]').spotlight(); });");
}

?>

<?php if ($link_enabled || $lightbox) : ?>
	<a href="<?php echo JRoute::_($url); ?>" <?php echo $rel;?> <?php echo $title; ?> <?php echo $target;?><?php echo $lightbox ? ' data-lightbox="on"' : ''; ?> <?php echo $spotlight; ?>><?php echo $content; ?></a>
<?php elseif ($spotlight) : ?>
	<div <?php echo $spotlight; ?>><?php echo $content; ?></div>
<?php else : ?>
	<?php echo $content; ?>
<?php endif;