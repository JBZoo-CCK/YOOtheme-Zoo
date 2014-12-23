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
	<div id="comment-<?php echo $comment->id; ?>" class="comment <?php if ($author->isJoomlaAdmin()) echo 'comment-byadmin'; ?>">

		<div class="comment-head">

			<?php if ($params->get('avatar', 0)) : ?>
				<div class="avatar"><?php echo $author->getAvatar(50); ?></div>
			<?php endif; ?>

			<?php if ($author->url) : ?>
				<h3 class="author"><a href="<?php echo JRoute::_($author->url); ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php echo $author->name; ?></a></h3>
			<?php else: ?>
				<h3 class="author"><?php echo $author->name; ?></h3>
			<?php endif; ?>

			<div class="meta">
				<?php echo $this->app->html->_('date', $comment->created, $this->app->date->format(JText::_('DATE_FORMAT_COMMENTS')), $this->app->date->getOffset()); ?>
				| <a class="permalink" href="#comment-<?php echo $comment->id; ?>">#</a>
			</div>

		</div>

		<div class="comment-body">

			<div class="content"><?php echo $this->app->comment->filterContentOutput($comment->content); ?></div>

			<?php if ($comment->getItem()->isCommentsEnabled()) : ?>
				<p><a class="reply" href="#" rel="nofollow"><?php echo JText::_('Reply'); ?></a>
				<?php if ($comment->canManageComments()) : ?>
					<?php echo ' | '; ?>
					<a class="edit" href="#" rel="nofollow"><?php echo JText::_('Edit'); ?></a>
					<?php echo ' | '; ?>
					<?php if ($comment->state != Comment::STATE_APPROVED) : ?>
						 <a href="<?php echo 'index.php?option=com_zoo&controller=comment&task=approve&comment_id='.$comment->id; ?>" rel="nofollow"><?php echo JText::_('Approve'); ?></a>
					<?php else: ?>
						<a href="<?php echo 'index.php?option=com_zoo&controller=comment&task=unapprove&comment_id='.$comment->id; ?>" rel="nofollow"><?php echo JText::_('Unapprove'); ?></a>
					<?php endif; ?>
					<?php echo ' | '; ?>
					<a href="<?php echo 'index.php?option=com_zoo&controller=comment&task=spam&comment_id='.$comment->id; ?>" rel="nofollow"><?php echo JText::_('Spam'); ?></a>
					<?php echo ' | '; ?>
					<a href="<?php echo 'index.php?option=com_zoo&controller=comment&task=delete&comment_id='.$comment->id; ?>" rel="nofollow"><?php echo JText::_('Delete'); ?></a>
				<?php endif; ?>
				</p>
			<?php endif; ?>

			<?php if ($comment->state != Comment::STATE_APPROVED) : ?>
				<div class="moderation"><?php echo JText::_('COMMENT_AWAITING_MODERATION'); ?></div>
			<?php endif; ?>

		</div>

	</div>

	<?php if ($comment->hasChildren()) : ?>
	<ul class="level<?php echo ++$level; ?>">
		<?php
		foreach ($comment->getChildren() as $comment) {
			echo $this->partial('comment', array('level' => $level, 'comment' => $comment, 'author' => $comment->getAuthor(), 'params' => $params));
		}
		?>
	</ul>
	<?php endif ?>

</li>