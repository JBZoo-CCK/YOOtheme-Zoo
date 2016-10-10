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
	<article id="comment-<?php echo $comment->id; ?>" class="uk-comment comment <?php if ($author->isJoomlaAdmin()) echo 'uk-comment-byadmin'; ?>">

		<header class="uk-comment-header uk-grid uk-grid-medium uk-flex-middle" uk-grid>

			<?php if ($params->get('avatar', 0)) : ?>
			<div class="uk-width-auto">
				<div class="uk-comment-avatar"><?php echo $author->getAvatar(50); ?></div>
			</div>
			<?php endif; ?>

			<div class="uk-width-expand">
				<?php if ($author->url) : ?>
					<h4 class="uk-comment-title"><a href="<?php echo JRoute::_($author->url); ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php echo $author->name; ?></a></h4>
				<?php else: ?>
					<h4 class="uk-comment-title"><?php echo $author->name; ?></h4>
				<?php endif; ?>

				<ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">
					<li><?php echo $this->app->html->_('date', $comment->created, $this->app->date->format(JText::_('DATE_FORMAT_COMMENTS')), $this->app->date->getOffset()); ?></li>
					<li><a href="#comment-<?php echo $comment->id; ?>">#</a></li>
				</ul>
			</div>

		</header>

		<div class="uk-comment-body">

			<p class="content"><?php echo $this->app->comment->filterContentOutput($comment->content); ?></p>

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
				<div class="uk-alert"><?php echo JText::_('COMMENT_AWAITING_MODERATION'); ?></div>
			<?php endif; ?>

		</div>

	</article>

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
