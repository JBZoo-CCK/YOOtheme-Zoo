<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Item order helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class ItemOrderHelper extends AppHelper {

	/**
	 * Translate pre ZOO 2.5 item order to new item order.
	 *
	 * @param string $order
	 * @return array the translated item order
	 * @since 2.0
	 */
	public function convert($order) {
		$orderings = array(
			'date'   => array('_itemcreated'),
			'rdate'  => array('_itemcreated', '_reversed'),
			'alpha'  => array('_itemname'),
			'ralpha' => array('_itemname', '_reversed'),
			'hits'   => array('_itemhits'),
			'rhits'  => array('_itemhits', '_reversed'),
			'random' => array('_random'));
		return isset($orderings[$order]) ? $orderings[$order] : array('_itemname');
	}

}