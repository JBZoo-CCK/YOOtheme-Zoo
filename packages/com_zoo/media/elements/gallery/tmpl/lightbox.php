<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->document->addScript('assets:js/lightbox.js');
$this->app->document->addStylesheet('assets:css/lightbox.css');
$this->app->document->addScriptDeclaration("jQuery(function($) { $('.zoo-gallery [data-lightbox]').lightbox(); });");

$css_classes  = ($params->get('corners', 'square') == 'round') ? 'round ' : '';
$css_classes .= ($params->get('effect') == 'zoom') ? 'zoom ' : '';
$css_classes .= ($params->get('margin')) ? 'margin ' : '';

$id = $this->identifier.'-'.uniqid();

?>
<div class="zoo-gallery" id="<?php echo $id; ?>">
	<div class="zoo-gallery-wall clearfix <?php echo $css_classes; ?>">

		<?php foreach ($thumbs as $image) : ?>

			<?php

				$lightbox  = '';
				$spotlight = '';
				$overlay   = '';

				/* Prepare Spotlight */
				if ($params->get('effect') == 'spotlight') {
					if ($params->get('spotlight_effect') && $params->get('spotlight_caption')) {
						$spotlight = 'data-spotlight="effect:'.$params->get('spotlight_effect').'"';
						$overlay = '<div class="overlay">'.$image['name'].'</div>';
					} else {
						$spotlight = 'data-spotlight="on"';
					}
				}

				/* Prepare Lightbox */
				if ($params->get('lightbox_group')) {
					$lightbox = 'data-lightbox="group:'.$this->identifier.'"';
				}

				if ($params->get('lightbox_caption')) {
					$lightbox .= ' title="'.$image['name'].'"';
				}

				/* Prepare Image */
				$content = '<img src="'.$image['thumb'].'" width="'.$image['thumb_width'].'" height="'.$image['thumb_height'].'" alt="'.$image['filename'].'" />'.$overlay;

			?>

			<a class="thumb" href="<?php echo $image['img']; ?>" <?php echo $lightbox; ?> <?php echo $spotlight; ?>><?php echo $content; ?></a>

		<?php endforeach; ?>

	</div>
</div>
<?php
	if ($params->get('effect') == 'opacity') {
		$this->app->document->addScriptDeclaration(sprintf("jQuery(function($) { $('%s').opacity(); });", '#'.$id .' .thumb'));
	}

