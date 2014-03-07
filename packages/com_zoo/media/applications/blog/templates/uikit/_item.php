<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<article class="uk-article">
<?php if ($item) : ?>
	<?php echo $this->renderer->render('item.teaser', array('view' => $this, 'item' => $item)); ?>
<?php endif; ?>
</article>