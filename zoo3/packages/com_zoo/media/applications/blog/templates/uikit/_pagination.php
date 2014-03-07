<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$url        = $this->pagination_link;
$pagination = $this->pagination;

?>

<?php if (!$pagination->getShowAll()) : ?>
<ul class="uk-pagination">

    <?php

    $html = '';

    if ($pagination->pages() > 1) {

        $range_start = max($pagination->current() - $pagination->range(), 1);
        $range_end   = min($pagination->current() + $pagination->range() - 1, $pagination->pages());

        if ($pagination->current() > 1) {
            $link  = $url;
            $html .= '<li><a href="'.JRoute::_($link).'">'.JText::_('First').'</a></li>';
            $link  = $pagination->current() - 1 == 1 ? $url : $pagination->link($url, $pagination->name().'='.($pagination->current() - 1));
            $html .= '<li><a href="'.JRoute::_($link).'">«</a></li>';
        }

        for ($i = $range_start; $i <= $range_end; $i++) {
            if ($i == $pagination->current()) {
                $html .= '<li class="uk-active"><span>'.$i.'</span>';
            } else {
                $link  = $i == 1 ? $url : $pagination->link($url, $pagination->name().'='.$i);
                $html .= '<li><a href="'.JRoute::_($link).'">'.$i.'</a></li>';
            }
        }

        if ($pagination->current() < $pagination->pages()) {
            $link  = $pagination->link($url, $pagination->name().'='.($pagination->current() + 1));
            $html .= '<li><a href="'.JRoute::_($link).'">»</a></li>';
            $link  = $pagination->link($url, $pagination->name().'='.($pagination->pages()));
            $html .= '<li><a href="'.JRoute::_($link).'">'.JText::_('Last').'</a></li>';
        }

    }

    echo $html;
    ?>
</ul>
<?php endif;