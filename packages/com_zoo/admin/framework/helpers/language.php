<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Language helper. Wrapper for JText and JLanguage
 * 
 * @package Framework.Helpers
 */
class LanguageHelper extends AppHelper {

	/**
	 * Translate a string into the current language
	 * 
	 * @param string $string The string to translate
	 * @param booolean $js_safe If the string should be made js safe (default: true)
	 * 
	 * @return string The translated string
	 * 
	 * @since 1.0.0
	 */
	public function l($string, $js_safe = false) {
		return $this->app->system->language->_($string, $js_safe);
	}
	
}