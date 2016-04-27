<?php
/**
 * @package   ZOO Quick Icons
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
jimport('joomla.filesystem.file');
if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php') || !JComponentHelper::getComponent('com_zoo', true)->enabled) {
	return;
}

require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

// make sure App class exists
if (!class_exists('App')) {
	return;
}

$zoo = App::getInstance('zoo');

$applications = $zoo->table->application->all(array('order' => 'name'));

if (empty($applications)) {
	return;
}

?>

<div class="sidebar-nav quick-icons">
	<h2 class="nav-header">ZOO</h2>
	<ul class="nav nav-list">
	<?php foreach ($applications as $application) : ?>
	<li>
		<a href="<?php echo JRoute::_('index.php?option='.$zoo->component->self->name.'&changeapp='.$application->id); ?>">
			<img style="width:24px; height:24px;" alt="<?php echo $application->name; ?>" src="<?php echo $application->getIcon(); ?>" />
			<span><?php echo $application->name; ?></span>
		</a>
	</li>
	<?php endforeach; ?>
	</ul>
</div>
