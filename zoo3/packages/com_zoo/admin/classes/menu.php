<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Class to deal with menus
 *
 * @package Component.Classes
 */
class AppMenu extends AppTree {

	/**
	 * The name of the menu
	 *
	 * @var string
	 * @since 2.0
	 */
	protected $_name;

	/**
	 * Class constructor
	 *
	 * @param string $name The name of the menu
	 */
	public function __construct($name) {
		parent::__construct();

		$this->_name = $name;
	}

	/**
	 * Render the menu
	 *
	 * @return string The html code for the menu
	 *
	 * @since 2.0
	 */
	public function render() {

		// create html
		$html = '<ul>';
		foreach ($this->_root->getChildren() as $child) {
			$html .= $child->render($this);
		}
		$html .= '</ul>';

		// decorator callbacks ?
		if (func_num_args()) {

			// parse html
			if ($xml = simplexml_load_string($html)) {

				foreach (func_get_args() as $callback) {
					if (is_callable($callback)) {
						$this->_map($xml, $callback);
					}
				}

				$html = $xml->asXML();
			}
		}

		return $html;
	}

	/**
	 * Call a method on evey child of the tree
	 *
	 * @param  SimpleXMLElement $xml      The xml to traverse
	 * @param  function         $callback The method to call on each child
	 * @param  array            $args     The arguments to pass on to the callback
	 *
	 * @since 2.0
	 */
	protected function _map(SimpleXMLElement $xml, $callback, $args = array()) {

		// init level
		if (!isset($args['level'])) {
			$args['level'] = 0;
		}

		// call function
		call_user_func($callback, $xml, $args);

		// raise level
		$args['level']++;

		// map to all children
		$children = $xml->children();
		if ($n = count($children)) {
			for ($i = 0; $i < $n; $i++) {
				$this->_map($children[$i], $callback, $args);
			}
		}
	}

}

/**
 * Class to represent a Menu Item
 *
 * @package Component.Classes
 */
class AppMenuItem extends AppTreeItem {

	/**
	 * Id of the menu item
	 *
	 * @var int
	 * @since 2.0
	 */
	protected $_id;

	/**
	 * Name of the menu item
	 *
	 * @var string
	 * @since 2.0
	 */
	protected $_name;

	/**
	 * Url of the menu item
	 *
	 * @var string
	 * @since 2.0
	 */
	protected $_link;

	/**
	 * Attributes to apply to the item
	 *
	 * @var array
	 * @since 2.0
	 */
	protected $_attributes;

	/**
	 * Class contructor
	 *
	 * @param int 	 $id         Id of the menu item
	 * @param string $name       Name of the menu item
	 * @param string $link       Link of the menu item
	 * @param array  $attributes List of attributes
	 */
	public function __construct($id = null, $name = '', $link = null, array $attributes = array()) {
		$this->_id		   = $id;
		$this->_name 	   = $name;
		$this->_link 	   = $link;
		$this->_attributes = $attributes;
	}

	/**
	 * Get the name of the menu Item
	 *
	 * @return string The name of the meu item
	 *
	 * @since 2.0
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Set the name of the menu item
	 *
	 * @param string $name The name
	 *
	 * @return AppMenuItem $this for chaining support
	 *
	 * @since 2.0
	 */
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}

	/**
	 * Get the id of the menu item
	 *
	 * @return int The menu item id
	 *
	 * @since 2.0
	 */
	public function getID() {
		return $this->_id ? $this->_id : parent::getId();
	}

	/**
	 * Get an attribute for the menu item
	 *
	 * @param  string $key The key to fetch
	 *
	 * @return string      The value for the attribute
	 *
	 * @since 2.0
	 */
	public function getAttribute($key) {

		if (isset($this->_attributes[$key])) {
			return $this->_attributes[$key];
		}

		return null;
	}

	/**
	 * Set an attribute for the menu item
	 *
	 * @param string $key   The key
	 * @param string $value The value
	 *
	 * @return AppMenuItem $this for chaining support
	 *
	 * @since 2.0
	 */
	public function setAttribute($key, $value) {
		$this->_attributes[$key] = $value;
		return $this;
	}

	/**
	 * Render the single menu item
	 *
	 * @return string The html for this menu item
	 *
	 * @since 2.0
	 */
	public function render() {
		$link   = $this->app->request->getVar('hidemainmenu') ? null : $this->_link;
		$html   = array('<li '.JArrayHelper::toString($this->_attributes).'>');
		$html[] = ($link ? '<a href="'.JRoute::_($link).'">' : '<span>').'<span>'.$this->getName().'</span>'.($link ? '</a>' : '</span>');

		if (count($this->getChildren())) {
			$html[] = '<ul>';
			foreach ($this->getChildren() as $child) {
				$html[] = $child->render();
			}
			$html[] = '</ul>';
		}

		$html[] = '</li>';

		return implode("\n", $html);
	}

}

/**
 * A decorator class for the menus
 */
class AppMenuDecorator {

    /**
     * Add item index and level to class attribute
     *
     * @param  SimpleXMLElement $node The node to add the index and level to
     * @param  array            $args Callback arguments
     *
     * @since 	2.0
     */
	public static function index(SimpleXMLElement $node, $args) {

		if ($node->getName() == 'ul') {

			// set ul level
			$level = ($args['level'] / 2) + 1;
			$node->addAttribute('class', trim($node->attributes()->class.' level'.$level));

			// set order/first/last for li
			$count = count($node->children());
			foreach ($node->children() as $i => $child) {
				$child->addAttribute('level', $level);
				$child->addAttribute('order', $i + 1);
				if ($i == 0) $child->addAttribute('first', 1);
				if ($i == $count - 1) $child->addAttribute('last', 1);
			}

		}

		if ($node->getName() == 'li') {

			// level and item order
			$css  = 'level'.$node->attributes()->level;
			$css .= ' item'.$node->attributes()->order;

			// first, last and parent
			if ($node->attributes()->first) $css .= ' first';
			if ($node->attributes()->last)  $css .= ' last';
			if (isset($node->ul))           $css .= ' parent';

			// add li css classes
			$node->attributes()->class = trim($node->attributes()->class.' '.$css);

			// add a/span css classes
			$children = $node->children();
			if ($firstChild = $children[0]) {
				$firstChild->addAttribute('class', trim($firstChild->attributes()->class.' '.$css));
			}
		}

		unset($node->attributes()->level, $node->attributes()->order, $node->attributes()->first, $node->attributes()->last);

	}

}