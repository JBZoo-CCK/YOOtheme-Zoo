<?php
/**
* @package   ZOO Comment
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include css
$zoo->document->addStylesheet('mod_zoocomment:tmpl/bubbles/style.css');

// include js
$zoo->document->addScript('component.site:assets/js/default.js');
$zoo->document->addScript('mod_zoocomment:tmpl/bubbles/bubbles.js');

// include IE7 specific css
$is_ie7 = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 7') !== false;
if ($is_ie7) $zoo->document->addStylesheet('mod_zoocomment:tmpl/bubbles/iehacks.css');

?>

<?php if ($count = count($comments)) : ?>

<section class="zoo-comments-bubbles grid-block">

	<?php $i = 0; foreach ($comments as $comment) : ?>

		<?php // set author name
			$author = $comment->getAuthor();
			$author->name = $author->name ? $author->name : JText::_('COM_ZOO_ANONYMOUS');
		?>

		<article class="grid-box <?php echo 'width'.intval(100 / $count); ?> <?php if ($author->isJoomlaAdmin()) echo 'comment-byadmin'; ?>">

			<p class="content match-height"><?php echo $zoo->comment->filterContentOutput($zoo->string->truncate($comment->content, $zoo->get('commentsmodule.max_characters'))); ?></p>

			<?php if ($params->get('show_avatar', 1) || $params->get('show_author', 1 || $params->get('show_meta', 1))) : ?>
			<p class="meta">

				<?php if ($params->get('show_avatar', 1)) : ?>
				<span class="image">
					<?php if ($author->url) : ?><a href="<?php echo $author->url; ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php endif; ?>
					<?php echo $author->getAvatar($params->get('avatar_size', 50)); ?>
					<?php if ($author->url) : ?></a><?php endif; ?>
				</span>
				<?php endif; ?>

				<?php if ($params->get('show_author', 1)) : ?>
				<span class="author">
					<?php if ($author->url) : ?><a href="<?php echo $author->url; ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php endif; ?>
					<?php echo $author->name; ?>
					<?php if ($author->url) : ?></a><?php endif; ?>
				</span>
				<?php endif; ?>

				<?php if ($params->get('show_meta', 1)) : ?>
				<span class="time">
					<?php echo $zoo->html->_('date', $comment->created, $zoo->date->format(JText::_('ZOO_COMMENT_MODULE_DATE_FORMAT')), $zoo->date->getOffset()); ?>
					| <a class="permalink" href="<?php echo JRoute::_($zoo->route->comment($comment)); ?>">#</a>
				</span>
				<?php endif; ?>

			</p>
			<?php endif; ?>

		</article>

	<?php $i++; endforeach; ?>

</section>

<?php else : ?>
<?php echo JText::_('COM_ZOO_NO_COMMENTS_FOUND'); ?>
<?php endif; ?>