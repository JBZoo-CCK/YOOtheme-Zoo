<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * HTML helper class.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class HTMLHelper extends AppHelper {

	/**
	 * Wrapper function
	 *
	 * @param string $type The function to call
	 *
	 * @return string The html output
	 * @since 2.0
	 */
	public function _($type) {

		// get arguments
		$args = func_get_args();

		// Check to see if we need to load a helper file
		$parts = explode('.', $type);

		if (count($parts) >= 2) {
			$func = array_pop($parts);
			$file = array_pop($parts);

			if (in_array($file, array('zoo', 'control')) && method_exists($this, $func)) {
				array_shift($args);
				return call_user_func_array(array($this, $func), $args);
			}
		}

		return call_user_func_array(array('JHTML', '_'), $args);
	}

	/**
	 * Get zoo datepicker.
	 *
	 * @param string $value The value
	 * @param string $name The html name
	 * @param string $id The html id
	 * @param string|array $attribs The html attributes
	 * @param boolean $time
	 *
	 * @return string datepicker html output
	 * @since 2.0
	 */
	public function calendar($value, $name, $id, $attribs = null, $time = false) {

		if (!defined('ZOO_CALENDAR_SCRIPT_DECLARATION')) {
			define('ZOO_CALENDAR_SCRIPT_DECLARATION', true);

			$this->app->document->addScript('assets:js/date.js');

			$translations = array(
				'closeText' => 'Done',
				'currentText' => 'Today',
				'dayNames' => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
				'dayNamesMin' => array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'),
				'dayNamesShort' => array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
				'monthNames' => array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
				'monthNamesShort' => array('JANUARY_SHORT', 'FEBRUARY_SHORT', 'MARCH_SHORT', 'APRIL_SHORT', 'MAY_SHORT',
					'JUNE_SHORT', 'JULY_SHORT', 'AUGUST_SHORT', 'SEPTEMBER_SHORT', 'OCTOBER_SHORT', 'NOVEMBER_SHORT', 'DECEMBER_SHORT'),
				'prevText' => 'Prev',
				'nextText' => 'Next',
				'weekHeader' => 'Wk',
				'appendText' => '(yyyy-mm-dd)'
			);

			foreach ($translations as $key => $translation) {
				$translations[$key] = is_array($translation) ? array_map(array('JText', '_'), $translation) : JText::_($translation);
			}

			$timepicker_translations = array_map(array('JText', '_'), array(
				'currentText' => 'Now',
				'closeText' => 'Done',
				'timeOnlyTitle' => 'Choose Time',
				'timeText' => 'Time',
				'hourText' => 'Hour',
				'minuteText' => 'Minute',
				'secondText' => 'Second'
			));

			$javascript = 'jQuery(function($) { $("body").Calendar({ translations: '.json_encode($translations).', timepicker_translations: '.json_encode($timepicker_translations).' });  });';

			$this->app->document->addScriptDeclaration($javascript);
		}

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		$data = $time ? ' data-timepicker="timepicker"' : '';

		return '<input'.$data.' style="width: 110px" type="text" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" '.$attribs.' />'
				.'<img src="'.JURI::root(true).'/templates/system/images/calendar.png'.'" class="zoo-calendar" />';
	}

	/**
	 * Get image resource info.
	 *
	 * @param string $image the image path
	 * @param int $width the image width
	 * @param int $height the image height
	 *
	 * @return array|null image info
	 * @since 2.0
	 */
	public function image($image, $width = null, $height = null) {

		$resized_image = $this->app->zoo->resizeImage(JPATH_ROOT.'/'.$image, $width, $height);
		$inner_path = $this->app->path->relative($resized_image);
		$path = JPATH_ROOT.'/'.$inner_path;

		if (is_file($path) && $size = getimagesize($path)) {

			$info['path'] = $path;
			$info['src'] = JURI::root().$inner_path;
			$info['mime'] = $size['mime'];
			$info['width'] = $size[0];
			$info['height'] = $size[1];
			$info['width_height'] = sprintf('width="%d" height="%d"', $info['width'], $info['height']);

			return $info;
		}

		return null;
	}

	/**
	 * Returns category select list html string.
	 *
	 * @param Application $application The application object
	 * @param array $options The options
	 * @param string $name The hmtl name
	 * @param string|array $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 * @param string $category The category id to build the select list for
	 *
	 * @return string category select list html
	 * @since 2.0
	 */
	public function categoryList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false, $category = 0, $prefix = '-&nbsp;', $spacer = '.&nbsp;&nbsp;&nbsp;', $indent = '&nbsp;&nbsp;') {

		// set options
		settype($options, 'array');
		reset($options);

		// get category tree list
		$list = $this->app->tree->buildList($category, $application->getCategoryTree(), array(), $prefix, $spacer, $indent);

		// create options
		foreach ($list as $category) {
			$options[] = $this->_('select.option', $category->id, $category->treename);
		}

		return $this->_('zoo.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Returns edit row html string.
	 *
	 * @param string $name The hmtl name
	 * @param string $value The hmtl value
	 *
	 * @return string the editor row html output
	 * @since 2.0
	 */
	public function editRow($name, $value) {

		$html[] = "\t<tr>\n";
		$html[] = "\t\t<td style=\"color:#666666;\">$name</td>\n";
		$html[] = "\t\t<td>$value</td>\n";
		$html[] = "\t</tr>\n";

		return implode("\n", $html);
	}

	/**
	 * Returns the country select list
	 *
	 * @param array $countries Countries
	 * @param string $name The hmtl name
	 * @param string $selected The selected value
	 * @param boolean $multiselect Show as multiselect
	 *
	 * @return string the country select list html output
	 * @since 2.0
	 */
	public function countrySelectList($countries, $name, $selected, $multiselect) {

		$options = array();
		if (!$multiselect) {
			$options[] = $this->app->html->_('select.option', '', '-'.JText::_('Select Country').'-');
		}

		foreach ($countries as $key => $country) {
			$options[] = $this->app->html->_('select.option', $key, JText::_($country));
		}

		$attribs = $multiselect ? 'size="'.max(min(count($options), 10), 3).'" multiple="multiple"' : '';

		return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
	}

	/**
	 * Returns layout select list html string.
	 *
	 * @param Type $type The type object
	 * @param string $layout_type The layout type filter
	 * @param array $options The options
	 * @param string $name The hmtl name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 *
	 * @return string the layout select list html output
	 * @since 2.0
	 */
	public function layoutList($type, $layout_type, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false) {

		// set options
		settype($options, 'array');
		reset($options);

		$layouts = $this->app->type->layouts($type, $layout_type);

		foreach ($layouts as $layout => $metadata) {
			$options[] = $this->_('select.option', $layout, $metadata->get('name'));
		}

		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Returns type select list html string.
	 *
	 * @param Application $application The Application object
	 * @param array $options The options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 * @param array $filter The type filter
	 *
	 * @return string the type select list html output
	 * @since 2.0
	 */
	public function typeList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $filter = array()) {

		// set options
		settype($options, 'array');
		reset($options);

		foreach ($application->getTypes() as $type) {
			if (empty($filter) || in_array($type->id, $filter)) {
				$options[] = $this->_('select.option', $type->id, JText::_($type->name));
			}
		}

		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Returns module select list html string.
	 *
	 * @param array $options The options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
     * @param boolean $published
	 *
	 * @return string the module select list html output
	 * @since 2.0
	 */
	public function moduleList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $published = false) {

		// set options
		settype($options, 'array');
		reset($options);

		// get modules
		$modules = $this->app->module->load($published);

		if (count($modules)) {

			foreach ($modules as $module) {
				$options[] = $this->app->html->_('select.option', $module->id, $module->title.' ('.$module->position.')');
			}

			return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
		}

		return JText::_("There are no modules to choose from.");
	}

	/**
	 * Returns plugin select list html string.
	 *
	 * @param array $options The options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 * @param string $folder The folder path to filter plugins by
	 * @param boolean $enabled Show selected plugins only
	 *
	 * @return string the plugin select list html output
	 * @since 2.0
	 */
	public function pluginList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $folder = false, $enabled = true) {

		// set options
		settype($options, 'array');
		reset($options);

		$plugins = JPluginHelper::getPlugin($folder);

		if (count($plugins)) {

			foreach ($plugins as $plugin) {
				if ($enabled && JPluginHelper::isEnabled($plugin->type, $plugin->name)) {
					$plugin_name = 'plg_'.$plugin->type.'_'.$plugin->name;
					JFactory::getLanguage()->load($plugin_name.'.sys', JPATH_ADMINISTRATOR);
					$options[] = $this->app->html->_('select.option', $plugin->name, $plugin_name);
				}
			}

			return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
		}

		return JText::_("There are no plugins to choose from.");
	}

	/**
	 * Returns author select list html string.
	 *
	 * @param array $options The options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 * @param boolean $show_registered_users Show registered users only
	 *
	 * @return string the author select list html output
	 * @since 2.0
	 */
	public function authorList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $show_registered_users = true) {
		$query = 'SELECT DISTINCT u.id AS value, u.name AS text'
				.' FROM #__users AS u'
				.' WHERE u.block = 0'
				.($show_registered_users ? '' : ' AND u.gid > 18');

		return $this->queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Returns item author select list html string.
	 *
	 * @param array $options The options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 *
	 * @return string the item author select list html output
	 * @since 2.0
	 */
	public function itemAuthorList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT DISTINCT u.id AS value, u.name AS text'
				.' FROM '.ZOO_TABLE_ITEM.' AS i'
				.' JOIN #__users AS u ON i.created_by = u.id';

		return $this->queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Returns item select list html string.
	 *
	 * @param Application $application The Application object
	 * @param array $options The options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 *
	 * @return string the item select list html output
	 * @since 2.0
	 */
	public function itemList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {

		// set options
		settype($options, 'array');
		reset($options);

		$query = 'SELECT DISTINCT c.item_id as value, a.name as text'
				.' FROM '.ZOO_TABLE_COMMENT.' AS c'
				.' LEFT JOIN '.ZOO_TABLE_ITEM.' AS a ON c.item_id = a.id'
				.' WHERE a.application_id = '.(int) $application->id
				.' ORDER BY a.name';

		$rows = $this->app->database->queryAssocList($query);

		foreach ($rows as $row) {
			$options[] = $this->_('select.option', $row['value'], $row['text']);
		}

		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Returns user access select list.
	 *
	 * @param array $options The options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 * @param array $exclude Access level ids to exclude
	 *
	 * @return string the user access list html output
	 * @since 2.0
	 */
	public function accessLevel($options, $name, $attribs = 'class="inputbox"', $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $exclude = array()) {

		// set options
		settype($options, 'array');
		reset($options);

		// set exclude
		$exclude = (array) $exclude;
		reset($exclude);

		$groups = $this->app->zoo->getGroups();
		foreach ($exclude as $key) {
			unset($groups[$key]);
		}
		foreach ($groups as $group) {
			$options[] = $this->_('select.option', $group->id, JText::_($group->name), $key, $text);
		}

		if (!isset($groups[$selected])) {
			$selected = $this->app->joomla->getDefaultAccess();
		}

		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Returns comment author select list html string.
	 *
	 * @param Application $application The Application object
	 * @param array $options The options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 *
	 * @return string the comment author select list html output
	 * @since 2.0
	 */
	public function commentAuthorList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {
		$query = "SELECT DISTINCT c.author AS value, c.author AS text"
				." FROM ".ZOO_TABLE_COMMENT." AS c"
				.' LEFT JOIN '.ZOO_TABLE_ITEM.' AS a ON c.item_id = a.id'
				." WHERE c.author <> ''"
				." AND a.application_id = ".$application->id
				." ORDER BY c.author";
		return $this->queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Returns select list html string.
	 *
	 * @param string $query The database query
	 * @param array $options The options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 *
	 * @return string select list html output
	 * @since 2.0
	 */
	public function queryList($query, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {

		// set options
		settype($options, 'array');
		reset($options);

		$options = array_merge($options, $this->app->database->queryObjectList($query));
		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Wrapper for Joomla 1.5/1.6
	 *
	 * @param array $data
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $optKey
	 * @param string $optText
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate
	 *
	 * @return string generic list html output
	 * @since 2.0
	 */
	public function genericList($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false) {
		$attributes['list.attr'] = $attribs;
		$attributes['id'] = $idtag;
		$attributes['list.translate'] = $translate;
		$attributes['option.key'] = $optKey;
		$attributes['option.text'] = $optText;
		$attributes['list.select'] = $selected;
		$attributes['option.text.toHtml'] = false;

		return $this->app->html->_('select.genericlist', $data, $name, $attributes);
	}

	//******************************
	//* ZOO Controls			   *
	//******************************

	/**
	 * Get array list
	 *
	 * @param array $array
	 * @param array $options
	 * @param string $name The html name
	 * @param array|string $attribs The html attributes
	 * @param string $key
	 * @param string $text
	 * @param string $selected The selected value
	 * @param string $idtag
	 * @param boolean $translate If the text should be translated.
	 *
	 * @return string array list html output
	 *
	 * @return type
	 */
	public function arrayList($array, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {

		// set options
		settype($options, 'array');
		reset($options);

		$options = array_merge($options, $this->listOptions($array));
		return $this->_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	/**
	 * Returns select option as JHTML compatible array.
	 *
	 * @param array $array
	 * @param string $value
	 * @param string $text
	 *
	 * @return array
	 * @since 2.0
	 */
	public function listOptions($array, $value = 'value', $text = 'text') {

		$options = array();

		if (is_array($array)) {
			foreach ($array as $val => $txt) {
				$options[] = $this->_('select.option', strval($val), $txt, $value, $text);
			}
		}

		return $options;
	}

	/**
	 * Returns directory select html string.
	 *
	 * @param string $directory
	 * @param string $filter
	 * @param string $name
	 * @param string $value
	 * @param array|string $attribs
	 *
	 * @return string directory select list html output
	 * @since 2.0
	 */
	public function selectdirectory($directory, $filter, $name, $value = null, $attribs = null) {

		// get directories
		$options = array($this->app->html->_('select.option', '', '- '.JText::_('Select Directory').' -'));
		$dirs = $this->app->path->dirs($directory, true, $filter);

		natsort($dirs);

		foreach ($dirs as $dir) {
			$options[] = $this->app->html->_('select.option', $dir, $dir);
		}

		return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $value);
	}

	/**
	 * Returns form textarea html string.
	 *
	 * @param string $name
	 * @param string $value
	 * @param array|string $attribs
	 *
	 * @return string form textarea html output
	 * @since 2.0
	 */
	public function textarea($name, $value = null, $attribs = null) {

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);

		return "\n\t<textarea name=\"$name\" $attribs >".$value."</textarea>\n";
	}

	/**
	 * Returns form text input html string.
	 *
	 * @param string $name
	 * @param string $value
	 * @param array|string $attribs
	 *
	 * @return string form text input html output
	 * @since 2.0
	 */
	public function text($name, $value = null, $attribs = null) {
		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		return $this->input('text', $name, $value, $attribs);
	}

	/**
	 * Returns form input html string.
	 *
	 * @param string $type
	 * @param string $name
	 * @param string $value
	 * @param array|string $attribs
	 *
	 * @return string form input html output
	 * @since 2.0
	 */
	public function input($type, $name, $value = null, $attribs = null) {

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		return "\n\t<input type=\"$type\" name=\"$name\" value=\"$value\" $attribs />\n";
	}

	/**
	 * Generates a yes/no radio list.
	 *
	 * @param   string  $name      The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $selected  The key that is selected
	 * @param   string  $yes       Language key for Yes
	 * @param   string  $no        Language key for no
	 * @param   string  $id        The id for the field
	 *
	 * @return  string  HTML for the radio list
	 */
	public function booleanlist($name, $attribs = null, $selected = null, $yes = 'JYES', $no = 'JNO', $id = false)
	{
		$arr = array(JHtml::_('select.option', '0', JText::_($no)), JHtml::_('select.option', '1', JText::_($yes)));
		return $this->radiolist($arr, $name, $attribs, 'value', 'text', (int) $selected, $id);
	}

	/**
	 * Generates an HTML radio list.
	 *
	 * @param   array    $data       An array of objects
	 * @param   string   $name       The value of the HTML name attribute
	 * @param   string   $attribs    Additional HTML attributes for the <select> tag
	 * @param   mixed    $optKey     The key that is selected
	 * @param   string   $optText    The name of the object variable for the option value
	 * @param   string   $selected   The name of the object variable for the option text
	 * @param   boolean  $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True if options will be translated
	 *
	 * @return  string HTML for the select list
	 *
	 * @since  11.1
	 */
	public function radiolist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false,
		$translate = false)
	{
		reset($data);
		$html = '';

		if (is_array($attribs))
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		$id_text = $idtag ? $idtag : $name;

		foreach ($data as $obj)
		{
			$k = $obj->$optKey;
			$t = $translate ? JText::_($obj->$optText) : $obj->$optText;
			$id = (isset($obj->id) ? $obj->id : null);

			$extra = '';
			$extra .= $id ? ' id="' . $obj->id . '"' : '';
			if (is_array($selected))
			{
				foreach ($selected as $val)
				{
					$k2 = is_object($val) ? $val->$optKey : $val;
					if ($k == $k2)
					{
						$extra .= ' selected="selected"';
						break;
					}
				}
			}
			else
			{
				$extra .= ((string) $k == (string) $selected ? ' checked="checked"' : '');
			}
			$html .= "\n\t" . '<input type="radio" name="' . $name . '"' . ' id="' . $id_text . $k . '" value="' . $k . '"' . ' ' . $extra . ' '
				. $attribs . '/>' . "\n\t" . '<label for="' . $id_text . $k . '"' . ' id="' . $id_text . $k . '-lbl" class="radiobtn">' . $t
				. '</label>';
		}
		$html .= "\n";
		return $html;
	}

}