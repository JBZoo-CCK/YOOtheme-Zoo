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

<div id="tag-area" class="uk-card uk-card-default">
    <input type="text" value="<?php echo implode(', ', $tags) ?>" placeholder="<?php echo JText::_('Add tag') ?>" />
	<p><?php echo JText::_('Choose from the most used tags') ?>:</p>
	<?php if (count($most)) : ?>
    <div class="tag-cloud uk-overflow-hidden">
        <?php foreach ($most as $tag) : ?>
        <a class="uk-button uk-button-default" title="<?php echo $tag->items . ' item' . ($tag->items != 1 ? 's' : '') ?>"><?php echo $tag->name ?></a>
        <?php endforeach ?>
    </div>
	<?php endif ?>
</div>

<script type="text/javascript">
    jQuery(function($) {
        var tagArea = $('#tag-area');

        tagArea.Tag({url: '<?php echo $link ?>', inputName: '<?php echo $this->getControlName('value', true) ?>'});

        $('.add-tag-button', tagArea).addClass('uk-button uk-button-primary');
        tagArea.on('addItem', 'input[type="text"]', function(e, li) {
            $(li).addClass('uk-button').find('.as-close').text('');
        });
    });
</script>
