<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// load script
$this->app->document->addScript('fields:zoofeed.js');

?>

<div class="zoo-feed">
	<?php echo $this->app->html->_('select.booleanlist', $control_name.'['.$name.']', null, $value); ?>
	<div class="input">
		<div class="input">
		<?php echo '<label class="hasTip" title="'.JText::_('OPTIONAL_FEED_TITLE').'" for="feed-title">'.JText::_('Feed title').'</label>'; ?>
		<?php echo $this->app->html->_('control.text', $control_name.'[feed_title]', $parent->getValue('feed_title'), array('id' => 'feed-title')); ?>
		</div>
		<div class="input">
			<?php echo '<label class="hasTip" title="'.JText::_('ALTERNATE_FEED_LINK').'" for="alternate-feed-link">'.JText::_('Alternate feed link').'</label>'; ?>
			<?php echo $this->app->html->_('control.text', $control_name.'[alternate_feed_link]', $parent->getValue('alternate_feed_link'), array('id' => 'alternate-feed-link')); ?>
		</div>
	</div>
</div>