<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// author
$user_name = JText::_('Guest');
if ($author = $item->created_by_alias) {
	$user_name = $author;
} else if (($user = $item->app->user->get($item->created_by)) && $user->name) {
	$user_name = $user->name;
}

?>

<html>
	<body>
		<p>Hi,</p>

		<p>You are receiving this email because you are administering the submissions at <?php echo $website_name; ?>. There has been a new submission by <?php echo $user_name; ?> - <?php echo $item->name; ?>.</p>

		<p>If you want to edit the new submission, click the following link: <a href="<?php echo $item_link; ?>"><?php echo $item_link; ?></a></p>
	</body>
</html>