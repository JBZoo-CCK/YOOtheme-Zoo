<?php
/**
* @package   ZOO Comment
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php if (count($comments)) : ?>

<ul class="uk-comment-list">

	<?php $i = 0; foreach ($comments as $comment) : ?>

		<?php // set author name
			$author = $comment->getAuthor();
			$author->name = $author->name ? $author->name : JText::_('COM_ZOO_ANONYMOUS');
		?>

		<li class="<?php if ($author->isJoomlaAdmin()) echo 'uk-comment-byadmin'; ?>">

			<article class="uk-comment">

				<header class="uk-comment-header">

					<?php if ($params->get('show_avatar', 1)) : ?>
					<div class="uk-comment-avatar">
						<?php if ($author->url) : ?><a href="<?php echo $author->url; ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php endif; ?>
						<?php echo $author->getAvatar($params->get('avatar_size', 50)); ?>
						<?php if ($author->url) : ?></a><?php endif; ?>
					</div>
					<?php endif; ?>

					<?php if ($params->get('show_author', 1)) : ?>
					<h4 class="uk-comment-title">
						<?php if ($author->url) : ?><a href="<?php echo $author->url; ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php endif; ?>
						<?php echo $author->name; ?>
						<?php if ($author->url) : ?></a><?php endif; ?>
					</h4>
					<?php endif; ?>

					<?php if ($params->get('show_meta', 1)) : ?>
					<div class="uk-comment-meta">
						<?php echo $zoo->html->_('date', $comment->created, $zoo->date->format(JText::_('ZOO_COMMENT_MODULE_DATE_FORMAT')), $zoo->date->getOffset()); ?>
						| <a class="permalink" href="<?php echo JRoute::_($zoo->route->comment($comment)); ?>">#</a>
					</div>
					<?php endif; ?>

				</header>

				<div class="uk-comment-body">
					<?php echo $zoo->comment->filterContentOutput($zoo->string->truncate($comment->content, $zoo->get('commentsmodule.max_characters'))); ?>
				</div>

			</article>

		</li>

	<?php $i++; endforeach; ?>

</ul>

<?php else : ?>
	<?php echo JText::_('COM_ZOO_NO_COMMENTS_FOUND'); ?>
<?php endif;