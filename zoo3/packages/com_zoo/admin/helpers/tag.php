<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The general helper class for tags
 *
 * @package Component.Helpers
 * @since 2.0
 */
class TagHelper extends AppHelper {

	/**
	 * Loads and gets a list of tags in JSON format
	 *
	 * @param int $application_id
	 * @param string $tag
	 *
	 * @return string Tags in JSON format
	 * @since 2.0
	 */
	public function loadtags($application_id, $tag) {

		$tags = array();
		if (!empty($tag)) {
			// get tags
			$tag_objects = $this->app->table->tag->getAll($application_id, $tag, '', 'a.name asc');

			foreach ($tag_objects as $tag) {
				$tags[] = $tag->name;
			}
		}

		return json_encode($tags);
	}

}
