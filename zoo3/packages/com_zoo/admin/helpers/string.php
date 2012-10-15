<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * The general String Helper.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class StringHelper extends AppHelper {

	/**
	 * wrapped class
	 * @var string
	 */
	protected $_class = 'JString';

	/**
	 * Map all functions to JRequest class
	 *
	 * @param string $method Method name
	 * @param array $args Method arguments
	 *
	 * @return mixed
	 */
	public function __call($method, $args) {
		return $this->_call(array($this->_class, $method), $args);
	}

	/**
	 * Truncates the input string.
	 *
	 * @param string $text input string
	 * @param int $length the length of the output string
	 * @param string $truncate_string the truncate string
	 *
	 * @return string The truncated string
	 * @since 2.0
	 */
	public function truncate($text, $length = 30, $truncate_string = '...') {

		if ($text == '') {
			return '';
		}

		if ($this->strlen($text) > $length) {
			$length -= min($length, strlen($truncate_string));
			$text = preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, $length + 1));

			return $this->substr($text, 0, $length).$truncate_string;
		} else {
			return $text;
		}
	}

	/**
	 * Get the transliteration array
	 *
	 * @return array Transliteration
	 */
	public function getTransliteration() {
		return array(
			'-' => array('\''),
			'a' => array('à', 'á', 'â', 'ã', 'ą', 'å', 'a', 'a'),
			'ae' => array('ä', 'æ'),
			'c' => array('c', 'c', 'ç', 'č', 'ć'),
			'd' => array('d', 'd'),
			'e' => array('è', 'é', 'ê', 'ë', 'e', 'ě', 'ę'),
			'g' => array('g', 'ğ'),
			'i' => array('ì', 'í', 'î', 'ï', 'ı'),
			'l' => array('l', 'l', 'l', 'ł'),
			'n' => array('ñ', 'n', 'n', 'ń'),
			'o' => array('ò', 'ó', 'ô', 'õ', 'ø', 'o', 'ó', 'ó'),
			'oe' => array('ö', 'œ'),
			'r' => array('r', 'ř'),
			's' => array('š', 's', 's', 'ş', 'ś'),
			't' => array('t', 't', 't'),
			'u' => array('ù', 'ú', 'û', 'u', 'µ'),
			'ue' => array('ü'),
			'y' => array('ÿ', 'ý'),
			'z' => array('ž', 'z', 'z', 'ż', 'ź'),
			'th' => array('þ'),
			'dh' => array('ð'),
			'ss' => array('ß')
		);
	}

	/**
	 * Sluggifies the input string.
	 *
	 * @param string $string input string
	 *
	 * @return string sluggified string
	 * @since 2.0
	 */
	public function sluggify($string) {

		$string = $this->strtolower((string) $string);

		foreach ($this->getTransliteration() as $replace => $keys) {
			foreach ($keys as $search) {
				$string = str_replace($search, $replace, $string);
			}
		}

		$string = preg_replace(array('/\s+/', '/[^\x{00C0}-\x{00D6}x{00D8}-\x{00F6}\x{00F8}-\x{00FF}\x{0370}-\x{1FFF}\x{4E00}-\x{9FAF}a-z0-9\-]/ui'), array('-', ''), $string);
		$string = preg_replace('/[-]+/u', '-', $string);
		$string = trim($string, '-');

		return trim($string);
	}

}