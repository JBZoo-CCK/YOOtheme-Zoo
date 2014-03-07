<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets css
$this->app->document->addStylesheet('elements:socialbookmarks/assets/css/socialbookmarks.css');

?>

<div class="yoo-zoo socialbookmarks">

	<?php foreach ($bookmarks as $name => $data) : ?>
		<?php $title = ($name == "email") ? JText::_('Recommend this Page') : JText::_('Add this Page to') . ' ' . ucfirst($name); ?>
		<a class="<?php echo $name ?>" onclick="<?php echo $data['click']; ?>" href="<?php echo $data['link']; ?>" title="<?php echo $title; ?>"></a>
	<?php endforeach; ?>

</div>
