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
<div class="pagination">

    <?php

    $html = '';

    if ($pagination->pages() > 1) {

        $range_start = max($pagination->current() - $pagination->range(), 1);
        $range_end = min($pagination->current() + $pagination->range() - 1, $pagination->pages());

        if ($pagination->current() > 1) {
            $link = $url;
            $html .= '<a class="first" href="' . JRoute::_($link) . '">' . JText::_('First') . '</a>';
            $link = $pagination->current() - 1 == 1 ? $url : $pagination->link($url, $pagination->name() . '=' . ($pagination->current() - 1));
            $html .= '<a class="previous" href="' . JRoute::_($link) . '">«</a>';
        }

        for ($i = $range_start; $i <= $range_end; $i++) {
            if ($i == $pagination->current()) {
                $html .= '<strong>' . $i . '</strong>';
            } else {
                $link = $i == 1 ? $url : $pagination->link($url, $pagination->name() . '=' . $i);
                $html .= '<a href="' . JRoute::_($link) . '">' . $i . '</a>';
            }
        }

        if ($pagination->current() < $pagination->pages()) {
            $link = $pagination->link($url, $pagination->name() . '=' . ($pagination->current() + 1));
            $html .= '<a class="next" href="' . JRoute::_($link) . '">»</a>';
            $link = $pagination->link($url, $pagination->name() . '=' . ($pagination->pages()));
            $html .= '<a class="last" href="' . JRoute::_($link) . '">' . JText::_('Last') . '</a>';
        }
    }

    echo $html;
    ?>
</div>
<?php endif;