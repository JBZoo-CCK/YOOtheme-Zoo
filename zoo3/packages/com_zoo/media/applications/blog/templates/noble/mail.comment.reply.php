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

<html>
	<body>
		<p>Hi <?php echo $name; ?>,</p>

		<p>You are receiving this email because you are watching the topic, <?php echo $item->name; ?> at <?php echo $website_name; ?>. This topic has received a reply.</p>
		
		<p>Quote: "<?php echo $comment->content; ?>"</p>

		<p>If you want to view the post made, click the following link: <a href="<?php echo $comment_link; ?>"><?php echo $comment_link; ?></a></p>

		<p>If you want to view the topic, click the following link: <a href="<?php echo $item_link; ?>"><?php echo $item_link; ?></a></p>

		<p>If you want to view the website, click on the following link: <a href="<?php echo $website_link; ?>"><?php echo $website_link; ?></a></p>

		<p>If you no longer wish to watch this topic, click the following link: <a href="<?php echo $unsubscribe_link; ?>"><?php echo $unsubscribe_link; ?></a></p>
	</body>
</html>