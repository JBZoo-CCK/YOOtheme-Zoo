<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

$html = '';

// check if show all
if ($this->_showall) {
    return $html;
}

// check if current page is valid
if ($this->_current > $this->_pages) {
    $this->_current = $this->_pages;
}

if ($this->_pages > 1) {

    $range_start = max($this->_current - $this->_range, 1);
    $range_end = min($this->_current + $this->_range - 1, $this->_pages);

    if ($this->_current > 1) {
        $link = $url;
        $html .= '<a class="first" href="' . JRoute::_($link) . '">' . JText::_('First') . '</a>';
        $link = $this->_current - 1 == 1 ? $url : $this->link($url, $this->_name . '=' . ($this->_current - 1));
        $html .= '<a class="previous" href="' . JRoute::_($link) . '">«</a>';
    }

    for ($i = $range_start; $i <= $range_end; $i++) {
        if ($i == $this->_current) {
            $html .= '<strong>' . $i . '</strong>';
        } else {
            $link = $i == 1 ? $url : $this->link($url, $this->_name . '=' . $i);
            $html .= '<a href="' . JRoute::_($link) . '">' . $i . '</a>';
        }
    }

    if ($this->_current < $this->_pages) {
        $link = $this->link($url, $this->_name . '=' . ($this->_current + 1));
        $html .= '<a class="next" href="' . JRoute::_($link) . '">»</a>';
        $link = $this->link($url, $this->_name . '=' . ($this->_pages));
        $html .= '<a class="last" href="' . JRoute::_($link) . '">' . JText::_('Last') . '</a>';
    }
}

echo $html;