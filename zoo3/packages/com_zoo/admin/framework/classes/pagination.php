<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class to provide pagination functionalities
 *
 * @package Framework.Classes
 */
class AppPagination {

	/**
	 * A reference to the global App object
	 *
	 * @var App
	 * @since 1.0.0
	 */
	public $app;

	/**
	 * Name of the paging http GET variable
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $_name;

	/**
	 * The total item count
	 *
	 * @var int
	 * @since 1.0.0
	 */
	protected $_total;

	/**
	 * The current page number
	 *
	 * @var int
	 * @since 1.0.0
	 */
	protected $_current;

	/**
	 * The number of items per pag
	 *
	 * @var int
	 * @since 1.0.0
	 */
	protected $_limit;

	/**
	 * The range for the displayed pagination pages
	 *
	 * @var int
	 * @since 1.0.0
	 */
	protected $_range;

	/**
	 * The total number of pages
	 *
	 * @var int
	 * @since 1.0.0
	 */
	protected $_pages;

	/**
	 * If we are showing all the items
	 *
	 * @var boolean
	 * @since 1.0.0
	 */
	protected $_showall = false;

	/**
	 * Constructor
	 *
	 * @param string $name The name of the pagination http GET variable
	 * @param int $total The total number of items
	 * @param int $current The current page (default: 1)
	 * @param int $limit The number of items per page (default: 10)
	 * @param int $range The range for the displayed page (default: 5)
	 */
	public function __construct($name, $total, $current = 1, $limit = 10, $range = 5) {

		// init vars
		$this->_name    = $name;
		$this->_total   = (int) max($total, 0);
		$this->_current = (int) max($current, 1);
		$this->_limit   = (int) max($limit, 1);
        $this->_range   = (int) max($range, 1);
		$this->_pages   = (int) ceil($this->_total / $this->_limit);

        // check if current page is valid
        if ($this->_current > $this->_pages) {
            $this->_current = $this->_pages;
        }

	}

    public function name() {
        return $this->_name;
    }

    public function total() {
        return $this->_total;
    }

    public function current() {
        return $this->_current;
    }

    public function limit() {
        return $this->_limit;
    }

    public function range() {
        return $this->_range;
    }

    public function pages() {
        return $this->_pages;
    }

	/**
	 * Get the show all items flag
	 *
	 * @return boolean True if we have to show all the items
	 *
	 * @since 1.0.0
	 */
	public function getShowAll() {
		return $this->_showall || $this->_pages < 2;
	}

 	/**
	 * Set the show all items flag
	 *
	 * @param boolean $showall If we have to show all the items
	 *
	 * @since 1.0.0
	 */
	public function setShowAll($showall) {
		$this->_showall = $showall;
	}

	/**
	 * Get the current limit start
	 *
	 * @return int The current limit start
	 *
	 * @since 1.0.0
	 */
	public function limitStart() {
		return ($this->_current - 1) * $this->_limit;
	}

	/**
	 * Get the link with the added GET parameters
	 *
	 * @param string $url The url to which we should add the GET parameter
	 * @param mixed $vars A list of variables to add to the url
	 *
	 * @return string The url with the added GET parameters
	 *
	 * @since 1.0.0
	 */
	public function link($url, $vars) {

		if (!is_array($vars)) {
			$vars = array($vars);
		}

		return $url.(strpos($url, '?') === false ? '?' : '&').implode('&', $vars);
	}

	/**
	 * Render the pagination
	 *
	 * @param string $url The url of the page on which we're adding the pagination
	 *
	 * @return string The html code of the pagination
	 *
	 * @since 1.0.0
	 */
    public function render($url = 'index.php', $layout = null) {

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
                $html .= '<a class="start" href="'.JRoute::_($link).'">&lt;&lt;</a>&nbsp;';
                $link  = $this->_current - 1 == 1 ? $url : $this->link($url, $this->_name.'='.($this->_current - 1));
                $html .= '<a class="previous" href="'.JRoute::_($link).'">&lt;</a>&nbsp;';
            }

            for ($i = $range_start; $i <= $range_end; $i++) {
                if ($i == $this->_current) {
                    $html .= '[<span>'.$i.'</span>]';
                } else {
                    $link  = $i == 1 ? $url : $this->link($url, $this->_name.'='.$i);
                    $html .= '<a href="'.JRoute::_($link).'">'.$i.'</a>';
                }
                $html .= "&nbsp;";
            }

            if ($this->_current < $this->_pages) {
                $link  = $this->link($url, $this->_name.'='.($this->_current + 1));
                $html .= '<a class="next" href="'.JRoute::_($link).'">&gt;&nbsp;</a>&nbsp;';
                $link  = $this->link($url, $this->_name.'='.($this->_pages));
                $html .= '<a class="end" href="'.JRoute::_($link).'">&gt;&gt;&nbsp;</a>&nbsp;';
            }

        }

        return $html;
    }
}