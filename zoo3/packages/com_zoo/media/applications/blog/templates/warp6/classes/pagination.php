<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppPagination
		The AppPagination Class. Provides Pagination functionality.
*/
class AppPagination {

	public $app;

	protected $_name;
	protected $_total;
	protected $_current;
	protected $_limit;
	protected $_range;
	protected $_pages;
	protected $_showall = false;

	/*
    	Function: constructor

		Parameters:
	      $name - Name of the pager http get var.
	      $total - Total item count.
	      $current - Current page.
	      $limit - Item limit per page.
	      $range - Range for displayed pagination pages.

	   Returns:
	      Void
 	*/
	public function __construct($name, $total, $current = 1, $limit = 10, $range = 5) {

		// init vars
		$this->_name    = $name;
		$this->_total   = (int) max($total, 0);
		$this->_current = (int) max($current, 1);
		$this->_limit   = (int) max($limit, 1);
        $this->_range   = (int) max($range, 1);
		$this->_pages   = (int) ceil($this->_total / $this->_limit);
	}

	/*
    	Function: getShowAll
 			Get show all items parameter.

	   Returns:
	      Boolean
 	*/
	public function getShowAll() {
		return $this->_showall;
	}

	/*
    	Function: setShowAll
 			Set show all items parameter.

		Parameters:
	      $showall - Show all parameter.

	   Returns:
	      Void
 	*/
	public function setShowAll($showall) {
		$this->_showall = $showall;
	}

	/*
    	Function: limitStart
 			Get limit current limit start.

	   Returns:
	      Int
 	*/
	public function limitStart() {
		return ($this->_current - 1) * $this->_limit;
	}

	/*
    	Function: link
 			Get link with added get parameter.

	   Returns:
			String - Link url
 	*/
	public function link($url, $vars) {

		if (!is_array($vars)) {
			$vars = array($vars);
		}

		return $url.(strpos($url, '?') === false ? '?' : '&').implode('&', $vars);
	}

	/*
    	Function: render
 			Render the paginator.

	   Returns:
			String - Pagination html
 	*/
    public function render($url = 'index.php') {

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
			$range_end   = min($this->_current + $this->_range - 1, $this->_pages);

            if ($this->_current > 1) {
				$link  = $url;
                $html .= '<a class="first" href="'.JRoute::_($link).'">'.JText::_('First').'</a>';
				$link  = $this->_current - 1 == 1 ? $url : $this->link($url, $this->_name.'='.($this->_current - 1));
				$html .= '<a class="previous" href="'.JRoute::_($link).'">«</a>';
            }

            for ($i = $range_start; $i <= $range_end; $i++) {
                if ($i == $this->_current) {
	                $html .= '<strong>'.$i.'</strong>';
                } else {
					$link  = $i == 1 ? $url : $this->link($url, $this->_name.'='.$i);
	                $html .= '<a href="'.JRoute::_($link).'">'.$i.'</a>';
                }
            }

            if ($this->_current < $this->_pages) {
				$link  = $this->link($url, $this->_name.'='.($this->_current + 1));
                $html .= '<a class="next" href="'.JRoute::_($link).'">»</a>';
				$link  = $this->link($url, $this->_name.'='.($this->_pages));
                $html .= '<a class="last" href="'.JRoute::_($link).'">'.JText::_('Last').'</a>';
            }

		}

        return $html;
    }

}