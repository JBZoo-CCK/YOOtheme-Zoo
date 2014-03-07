<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * User agent helper class, detect browser, version and operating system
 * Based on Simple PHP User agent (http://github.com/ornicar/php-user-agent, Thibault Duplessis <thibault.duplessis@gmail.com>, MIT License)
 * 
 * @package Framework.Helpers 
 */
class UseragentHelper extends AppHelper {

	/**
	 * User agent info
	 * 
	 * @var array
	 * @since 1.0.0
	 */
	public $_info;

	/**
	 * Get the ip address
	 * 
	 * @return string The ip address
	 * 
	 * @since 1.0.0
	 */
	public function ip() {

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * Get the host name
	 * 
	 * @return string The host name
	 * 
	 * @since 1.0.0
	 */
	public function host() {
		return gethostbyaddr($this->ip());
	}

	/**
	 * Get browser name
	 * 
	 * @return The Browser name
	 * 
	 * @since 1.0.0
	 */
	public function browser() {
		
		if (empty($this->_info)) {
			$this->_info = $this->parse();
		}
		
		return $this->_info['browser_name'];
	}

	/**
	 * Get the browser version
	 * 
	 * @return The browser version
	 * 
	 * @since 1.0.0
	 */
	public function version() {
		
		if (empty($this->_info)) {
			$this->_info = $this->parse();
		}
		
		return $this->_info['browser_version'];
	}

	/**
	 * Get the operating system
	 * 
	 * @return The operating system
	 * 
	 * @since 1.0.0
	 */
	public function os() {
		
		if (empty($this->_info)) {
			$this->_info = $this->parse();
		}
		
		return $this->_info['operating_system'];
	}

	/**
	 * Parse a user agent string.
	 *
	 * @param string $userAgentString defaults to $_SERVER['HTTP_USER_AGENT'] if empty
	 * 
	 * @return array ( the user agent informations
	 *            'browser_name'      => 'firefox',
	 *            'browser_version'   => '3.6',
	 *            'operating_system'  => 'linux'
	 *          )
	 * 
	 * @since 1.0.0
	 */
	public function parse($userAgentString = null) {
		
		// use current user agent string as default
		if (!$userAgentString) {
			$userAgentString = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
		}
		
		// parse quickly (with medium accuracy)
		$informations = $this->doParse($userAgentString);
		
		// run some filters to increase accuracy
		foreach ($this->getFilters() as $filter) {
			$this->$filter($informations);
		}
		
		return $informations;
	}
	
	/**
	 * Detect quickly informations from the user agent string
	 *
	 * @param string $userAgentString user agent string
	 *
	 * @return array user agent informations array
	 * 
	 * @since 1.0.0
	 */
	public function doParse($userAgentString) {
		
		$userAgent = array(
			'string' => $this->cleanUserAgentString($userAgentString) ,
			'browser_name' => null,
			'browser_version' => null,
			'operating_system' => null
		);

		if (empty($userAgent['string'])) {
			return $userAgent;
		}

		// build regex that matches phrases for known browsers
		// (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
		// version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"
		$pattern = '#(' . join('|', $this->getKnownBrowsers()) . ')[/ ]+([0-9]+(?:\.[0-9]+)?)#';

		// Find all phrases (or return empty array if none found)
		if (preg_match_all($pattern, $userAgent['string'], $matches)) {

			// Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
			// Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
			// in the UA).  That's usually the most correct.
			$i = count($matches[1]) - 1;

			if (isset($matches[1][$i])) {
				$userAgent['browser_name'] = $matches[1][$i];
			}

			if (isset($matches[2][$i])) {
				$userAgent['browser_version'] = $matches[2][$i];
			}
		}

		// Find operating system
		$pattern = '#' . join('|', $this->getKnownOperatingSystems()) . '#';

		if (preg_match($pattern, $userAgent['string'], $match)) {
			if (isset($match[0])) {
				$userAgent['operating_system'] = $match[0];
			}
		}

		return $userAgent;
	}

	/**
	 * Make user agent string lowercase, and replace browser aliases
	 *
	 * @param string $userAgentString the dirty user agent string
	 * 
	 * @return string the clean user agent string
	 * 
	 * @since 1.0.0
	 */
	public function cleanUserAgentString($userAgentString) {
		
		// clean up the string
		$userAgentString = trim(strtolower($userAgentString));

		// replace browser names with their aliases
		$userAgentString = strtr($userAgentString, $this->getKnownBrowserAliases());

		// replace operating system names with their aliases
		$userAgentString = strtr($userAgentString, $this->getKnownOperatingSystemAliases());

		return $userAgentString;
	}

	/**
	 * Get the list of filters that get called when parsing a user agent
	 *
	 * @return array list of valid callables
	 * 
	 * @since 1.0.0
	 */
	public function getFilters() {
		return array(
			'filterGoogleChrome',
			'filterSafariVersion',
			'filterOperaVersion',
			'filterYahoo'
		);
	}

	/**
	 * Add a filter to be called when parsing a user agent
	 *
	 * @param string $filter name of the filter method
	 * 
	 * @since 1.0.0
	 */
	public function addFilter($filter) {
		$this->filters+= $filter;
	}
	
	/**
	 * Get known browsers
	 *
	 * @return array the browsers
	 * 
	 * @since 1.0.0
	 */
	public function getKnownBrowsers() {
		return array(
			'msie',
			'firefox',
			'safari',
			'webkit',
			'opera',
			'netscape',
			'konqueror',
			'gecko',
			'chrome',
			'googlebot',
			'iphone',
			'msnbot',
			'applewebkit'
		);
	}

	/**
	 * Get known browser aliases
	 *
	 * @return array the browser aliases
	 * 
	 * @since 1.0.0
	 */
	public function getKnownBrowserAliases() {
		return array(
			'shiretoko' => 'firefox',
			'namoroka' => 'firefox',
			'shredder' => 'firefox',
			'minefield' => 'firefox',
			'granparadiso' => 'firefox'
		);
	}
	
	/**
	 * Get known operating system
	 *
	 * @return array the operating systems
	 * 
	 * @since 1.0.0
	 */
	public function getKnownOperatingSystems() {
		return array(
			'windows',
			'macintosh',
			'linux',
			'freebsd',
			'unix',
			'iphone',
			'ipad'
		);
	}
	
	/**
	 * Get known operating system aliases
	 *
	 * @return array the operating system aliases
	 * 
	 * @since 1.0.0
	 */
	public function getKnownOperatingSystemAliases() {
		return array();
	}
	
	/**
	 * Google chrome has a safari like signature
	 * 
	 * @param array $userAgent Informations on the user agents
	 * 
	 * @since 1.0.0
	 */
	public function filterGoogleChrome(&$userAgent) {
		if ('safari' === $userAgent['browser_name'] && strpos($userAgent['string'], 'chrome/')) {
			$userAgent['browser_name'] = 'chrome';
			$userAgent['browser_version'] = preg_replace('|.+chrome/([0-9]+(?:\.[0-9]+)?).+|', '$1', $userAgent['string']);
		}
	}
	
	/**
	 * Safari version is not encoded "normally"
	 *  
	 * @param array $userAgent Informations on the user agents
	 * 
	 * @since 1.0.0
	 */
	public function filterSafariVersion(&$userAgent) {
		if ('safari' === $userAgent['browser_name'] && strpos($userAgent['string'], ' version/')) {
			$userAgent['browser_version'] = preg_replace('|.+\sversion/([0-9]+(?:\.[0-9]+)?).+|', '$1', $userAgent['string']);
		}
	}
	
	/**
	 * Opera 10.00 (and higher) version number is located at the end
	 *  
	 * @param array $userAgent Informations on the user agents
	 * 
	 * @since 1.0.0
	 */
	public function filterOperaVersion(&$userAgent) {
		if ('opera' === $userAgent['browser_name'] && strpos($userAgent['string'], ' version/')) {
			$userAgent['browser_version'] = preg_replace('|.+\sversion/([0-9]+\.[0-9]+)\s*.*|', '$1', $userAgent['string']);
		}
	}
	
	/**
	 * Yahoo bot has a special user agent string
	 *  
	 * @param array $userAgent Informations on the user agents
	 * 
	 * @since 1.0.0
	 */
	public function filterYahoo(&$userAgent) {
		if (null === $userAgent['browser_name'] && strpos($userAgent['string'], 'yahoo! slurp')) {
			$userAgent['browser_name'] = 'yahoobot';
		}
	}

}