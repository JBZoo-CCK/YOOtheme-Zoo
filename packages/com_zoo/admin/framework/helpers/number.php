<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper for formatting numbers and deal with currencies
 * Based on Number Helper (http://cakephp.org, Cake Software Foundation, Inc., MIT License)
 * 
 * @package Framework.Helpers
 */
class NumberHelper extends AppHelper {

	/**
	 * Default currency format
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	public $currency = 'EUR';
    
	/**
	 * List of supported currencies
	 * 
	 * @var array
	 * @since 1.0.0
	 */
	protected $_currencies = array(
		'USD' => array(
			'currency' => 'USD', 'before' => '$', 'after' => '', 'zero' => 0,
			'places' => 2, 'thousands' => ',', 'decimals' => '.', 'negative' => '-'
		),
		'GBP' => array(
			'currency' => 'GBP', 'before'=> '£', 'after' => '', 'zero' => 0,
			'places' => 2, 'thousands' => ',', 'decimals' => '.', 'negative' => '-'
		),
		'EUR' => array(
			'currency' => 'EUR', 'before' => '', 'after' => ' €', 'zero' => 0,
			'places' => 2, 'thousands' => '.', 'decimals' => ',', 'negative' => '-'
		),
		'DEFAULT' => array(
			'before' => '', 'after' => '', 'zero' => '0', 'places' => 2,
			'thousands' => ',', 'decimals' => '.', 'negative' => '-'
		)
	);

	/**
	 * Formats a number with a level of precision.
	 *
	 * @param float $number A floating point number.
	 * @param integer $precision The precision of the returned number.
	 * 
	 * @return float The formatted number
	 * 
	 * @since 1.0.0
	 */
	public function precision($number, $precision = 3) {
		return sprintf("%01.{$precision}f", $number);
	}

	/**
	 * Returns a formatted-for-humans file size.
	 *
	 * @param integer $size Size in bytes
	 *
	 * @return string Human readable size
	 *
	 * @since 1.0.0
	 */
	public function toReadableSize($size) {
		switch (true) {
			case $size < 1024:
				return sprintf(__n('%d Byte', '%d Bytes', $size, true), $size);
			case round($size / 1024) < 1024:
				return sprintf(__('%d KB', true), $this->precision($size / 1024, 0));
			case round($size / 1024 / 1024, 2) < 1024:
				return sprintf(__('%.2f MB', true), $this->precision($size / 1024 / 1024, 2));
			case round($size / 1024 / 1024 / 1024, 2) < 1024:
				return sprintf(__('%.2f GB', true), $this->precision($size / 1024 / 1024 / 1024, 2));
			default:
				return sprintf(__('%.2f TB', true), $this->precision($size / 1024 / 1024 / 1024 / 1024, 2));
		}
	}

	/**
	 * Formats a number into a percentage string.
	 *
	 * @param float $number A floating point number
	 * @param integer $precision The precision of the returned number
	 * 
	 * @return string Percentage string
	 * 
	 * @since 1.0.0
	 */
	public function toPercentage($number, $precision = 2) {
		return $this->precision($number, $precision) . '%';
	}

	/**
	 * Formats a number into a currency format.
	 *
	 * @param float $number A floating point number
	 * @param integer $options if int then places, if string then before, if (,.-) then use it or array with places and before keys
	 * 
	 * @return string formatted number
	 * 
	 * @since 1.0.0
	 */
	public function format($number, $options = false) {
		$places = 0;
		
		if (is_int($options)) {
			$places = $options;
		}

		$separators = array(',', '.', '-', ':');

		$before = $after = null;
		if (is_string($options) && !in_array($options, $separators)) {
			$before = $options;
		}
		
		$thousands = ',';
		if (!is_array($options) && in_array($options, $separators)) {
			$thousands = $options;
		}
		
		$decimals = '.';
		if (!is_array($options) && in_array($options, $separators)) {
			$decimals = $options;
		}

		if (is_array($options)) {
			$options = array_merge(array('before'=>'$', 'places' => 2, 'thousands' => ',', 'decimals' => '.'), $options);
			extract($options);
		}

		return $before.number_format($number, $places, $decimals, $thousands).$after;
	}

	/**
	 * Formats a number into a currency format.
	 *
	 * ### Options
	 *
	 * - `currency` - Shortcut to default options. Valid values are 'USD', 'EUR', 'GBP', otherwise set at least 'before' and 'after' options.
	 * - `before` - The currency symbol to place before whole numbers ie. '$'
	 * - `after` - The currency symbol to place after decimal numbers ie. 'c'. Set to boolean false to 
	 *    use no decimal symbol.  eg. 0.35 => $0.35.
	 * - `zero` - The text to use for zero values, can be a string or a number. ie. 0, 'Free!'
	 * - `places` - Number of decimal places to use. ie. 2
	 * - `thousands` - Thousands separator ie. ','
	 * - `decimals` - Decimal separator symbol ie. '.'
	 * - `negative` - Symbol for negative numbers. If equal to '()', the number will be wrapped with ( and )
	 * - `escape` - Should the output be htmlentity escaped? Defaults to true
	 *
	 * @param float $number The number to format
	 * @param array $options A list of options (see full description)
	 * 
	 * @return string Number formatted as a currency.
	 * 
	 * @since 1.0.0
	 */
	public function currency($number, $options = array()) {

		$currency = isset($options['currency']) ? $options['currency'] : $this->currency;
		$default  = isset($this->_currencies[$currency]) ? $this->_currencies[$currency] : $this->_currencies['DEFAULT'];
		$options  = array_merge($default, $options);
		$result   = null;

		// zero ?
		if ($number == 0 && $options['zero'] !== 0) {
			return $options['zero'];
		}

		// apply format
		$result = $this->format(abs($number), $options);

		// negative ?
		if ($number < 0) {
			$result = $options['negative'] == '()' ? '('.$result.')' : $options['negative'].$result;
		}

		return $result;
	}

	/**
	 * Add a currency format to the Number helper.  Makes reusing
	 * currency formats easier.
	 *
	 * {{{ $number->addFormat('NOK', array('before' => 'Kr. ')); }}}
	 * 
	 * You can now use `NOK` as a shortform when formatting currency amounts.
	 *
	 * {{{ $number->currency($value, 'NOK'); }}}
	 *
	 * Added formats are merged with the following defaults.
	 *
	 * {{{
	 *	array(
	 *		'before' => '$', 'after' => 'c', 'zero' => 0, 'places' => 2, 'thousands' => ',',
	 *		'decimals' => '.', 'negative' => '()', 'escape' => true
	 *	)
	 * }}}
	 *
	 * @param string $formatName The format name to be used in the future.
	 * @param array $options The array of options for this format.
	 * 
	 * @see NumberHelper::currency()
	 * 
	 * @since 1.0.0
	 */
	public function addFormat($formatName, $options) {
		$this->_currencies[$formatName] = $options + $this->_currencyDefaults;
	}

}