<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// set author name
$author->name = $author->name ? $author->name : JText::_('Anonymous');

?>
<li>
	<div id="comment-<?php echo $comment->id; ?>" class="uk-comment comment <?php if ($author->isJoomlaAdmin()) echo 'uk-comment-byadmin'; ?>">

		<div class="uk-comment-header">

			<?php if ($params->get('avatar', 0)) : ?>
				<div class="uk-comment-avatar"><?php echo $author->getAvatar(50); ?></div>
			<?php endif; ?>

			<?php if ($author->url) : ?>
				<h4 class="uk-comment-title"><a href="<?php echo JRoute::_($author->url); ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php echo $author->name; ?></a></h3>
			<?php else: ?>
				<h4 class="uk-comment-title"><?php echo $author->name; ?></h3>
			<?php endif; ?>

			<div class="uk-comment-meta">
				<?php echo $this->app->html->_('date', $comment->created, $this->app->date->format(JText::_('DATE_FORMAT_COMMENTS')), $this->app->date->getOffset()); ?>
				| <a href="#comment-<?php echo $comment->id; ?>">#</a>
			</div>

		</div>

		<div class="uk-comment-body">

			<p><?php echo $this->app->comment->filterContentOutput($comment->content); ?></p>

			<?php if ($comment->getItem()->isCommentsEnabled()) : ?>
				<p class="reply"><a href="#" rel="nofollow"><?php echo JText::_('Reply'); ?></a></p>
			<?php endif; ?>

			<?php if ($comment->state != Comment::STATE_APPROVED) : ?>
				<div class="uk-alert"><?php echo JText::_('COMMENT_AWAITING_MODERATION'); ?></div>
			<?php endif; ?>

		</div>

	</div>

	<?php if ($comment->hasChildren()) : ?>
	<ul>
		<?php
		foreach ($comment->getChildren() as $comment) {
			echo $this->partial('comment', array('level' => $level, 'comment' => $comment, 'author' => $comment->getAuthor(), 'params' => $params));
		}
		?>
	</ul>
	<?php endif ?>

</li>