<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Exporter for K2 items and categories
 *
 * @package Component.Classes.Exporters
 */
class AppExporterK2 extends AppExporter {

	/**
	 * Class Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->_name = 'K2';
	}

	/**
	 * If K2 is installed on the system
	 *
	 * @return boolean If K2 is installed on the system
	 *
	 * @since 2.0
	 */
	public function isEnabled() {
		$path_to_xml = JPATH_ADMINISTRATOR . '/components/com_k2/';
		if ((JFile::exists($path_to_xml.'manifest.xml') and $data = JApplicationHelper::parseXMLInstallFile($path_to_xml.'manifest.xml'))
				or (JFile::exists($path_to_xml.'k2.xml') and $data = JApplicationHelper::parseXMLInstallFile($path_to_xml.'k2.xml'))) {
            return (version_compare($data['version'], '2.1') >= 0);
		}

		return false;
	}

	/**
	 * Do the real export of items and categories
	 *
	 * @return string The JSON dump of the items and categories
	 *
	 * @since 2.0
	 */
	public function export() {

		$db = $this->app->database;

		// get k2 categories
	    $query = "SELECT a.*, b.name AS extra_field_group_name "
	    		." FROM #__k2_categories AS a"
	       		." LEFT JOIN #__k2_extra_fields_groups AS b ON b.id = a.extraFieldsGroup";
	    $categories = $db->queryObjectList($query, 'id');

		// sanatize category aliases
		$aliases = array();
		foreach ($categories as $category) {

			$i = 2;
			$alias = $this->app->string->sluggify($category->alias);
			while (in_array($alias, $aliases)) {
				$alias = $category->alias . '-' . $i++;
			}
			$category->alias = $alias;

			// remember used aliases to ensure unique aliases
			$aliases[] = $category->alias;
		}

		// export categories
		foreach ($categories as $category) {

			// assign attributes
			$data = array();
			foreach ($this->category_attributes as $attribute) {
				if (isset($category->$attribute)) {
					$data[$attribute] = $category->$attribute;
				}
			}

			// sanatize parent
			if ($category->parent && isset($categories[$category->parent])) {
				$data['parent'] = $categories[$category->parent]->alias;
			}

			// add category
			if ($category->image) {
				$data['content']['image'] = '/media/k2/categories/'.$category->image;
			}
			$this->_addCategory($category->name, $category->alias, $data);
		}

		// get k2 items
	    $query = "SELECT * FROM #__k2_items";
	    $items = $db->queryObjectList($query, 'id');

	    // get k2 extra fields
	    $query 		  = "SELECT * FROM #__k2_extra_fields";
	    $extra_fields = $db->queryObjectList($query, 'id');

	    // get k2 tags
	    $query = "SELECT a.itemID, b.name"
	    		." FROM #__k2_tags_xref as a"
	    		." JOIN #__k2_tags AS b ON a.tagID = b.id";
	    $tag_result = $db->queryObjectList($query);
	    $tags = array();
	    foreach ($tag_result as $tag) {
	    	$tags[$tag->itemID][] = $tag->name;
	    }

		// sanatize item aliases
		$aliases = array();
		foreach ($items as $item) {
			$i = 2;
			$alias = $this->app->string->sluggify($item->alias);
			while (in_array($alias, $aliases)) {
				$alias = $item->alias . '-' . $i++;
			}
			$item->alias = $alias;

			// remember used aliases to ensure unique aliases
			$aliases[] = $item->alias;

		}

		// export items
		foreach ($items as $item) {
            if (!$item->trash) {
                if (!$type = $categories[$item->catid]->extra_field_group_name) {
                    $type = JText::_('K2-Unassigned');
                }

                $this->_addK2Item($item, $extra_fields, $categories, $tags, $type);
            }
		}

		return parent::export();

	}

	/**
	 * Add an item to the list of items to export
	 *
	 * @param object $item The item to export
	 * @param array $extra_fields The extra fields for this item
	 * @param array $categories The categories for this item
	 * @param array $tags The item tags
	 * @param string $group The item group
	 *
	 * @return AppExporterK2 $this for chaining support
	 */
	protected function _addK2Item($item, $extra_fields, $categories = array(), $tags = array(), $group = 'default') {

		$data = array();
		foreach ($this->item_attributes as $attribute) {
			if (isset($item->$attribute)) {
				$data[$attribute] = $item->$attribute;
			}
		}
		// add author
		$data['author'] = $this->app->user->get($item->created_by)->username;

		// add state
		$data['state'] = $item->published;

		// add category
		$data['categories'][] = $categories[$item->catid]->alias;

		// add tags
		$data['tags'] = isset($tags[$item->id]) ? $tags[$item->id] : array();

		// add item content
		$i = 0;
		$data['elements'][$i]['type'] = 'textarea';
		$data['elements'][$i]['name'] = 'content';
		$data['elements'][$i++]['data'] = array(array('value' => $item->introtext), array('value' => $item->fulltext));

		$data['elements'][$i]['type'] = 'image';
		$data['elements'][$i]['name'] = 'image';
		$data['elements'][$i++]['data'] = array('file' => 'media/k2/items/src/'.md5("Image".$item->id).'.jpg');

		// add extra fields
        if (isset($item->extra_fields)) {
            foreach (json_decode($item->extra_fields) as $element) {

                $extrafield = $extra_fields[$element->id];

                switch ($extrafield->type) {
                    case 'textfield':
						$data['elements'][$i]['type'] = 'text';
						$data['elements'][$i]['name'] = $extrafield->name;
						$data['elements'][$i++]['data'] = array(array('value' => $element->value));
                        break;
                    case 'textarea':
						$data['elements'][$i]['type'] = 'textarea';
						$data['elements'][$i]['name'] = $extrafield->name;
						$data['elements'][$i++]['data'] = array(array('value' => $element->value));
                        break;
                    case 'select':
                    case 'multipleSelect':
						$data['elements'][$i]['type'] = 'select';
						$data['elements'][$i]['name'] = $extrafield->name;
						$data['elements'][$i++]['data'] = array('option' => $element->value);
                        break;
                    case 'radio':
						$data['elements'][$i]['type'] = 'radio';
						$data['elements'][$i]['name'] = $extrafield->name;
						$data['elements'][$i++]['data'] = array('option' => $element->value);
                        break;
                    case 'link':
						$data['elements'][$i]['type'] = 'link';
						$data['elements'][$i]['name'] = $extrafield->name;
						$data['elements'][$i++]['data'] = array(array('text' => $element->value[0], 'value' => $element->value[1]));
                        break;
                }
            }
        }

		parent::_addItem($item->title, $item->alias, $group, $data);
	}

}