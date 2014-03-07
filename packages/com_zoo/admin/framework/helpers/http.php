<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper to deal with HTTP requests
 *
 * @package Framework.Helpers
 */
class HttpHelper extends AppHelper {

	/**
	 * The current transport class
	 *
	 * @var AppHttp
	 */
	public $transport;

	/**
	 * Available transport classes
	 *
	 * @var array
	 * @since 1.0.0
	 */
	public $transports = array('AppHttpCurl', 'AppHttpStreams', 'AppHttpSocket');

	/**
	 * Constructor
	 *
	 * @param App $app A reference to the global App object
	 */
	public function __construct($app) {
		parent::__construct($app);

		// check available library support
		foreach ($this->transports as $classname) {
			$transport = new $classname();
			if ($transport->available()) {
				$this->transport =& $transport;
				break;
			}
		}
	}

	/**
	 * Execute a GET http request
	 *
	 * @param string $url The url to GET
	 * @param array $options Options for the request
	 *
	 * @see AppHttp::request()
	 *
	 * @return string The result of the request
	 *
	 * @since 1.0.0
	 */
	public function get($url, $options = array()) {
		return $this->request($url, $options);
	}

	/**
	 * Execute a POST http request
	 *
	 * @param string $url The url to POST to
	 * @param mixed $data The data to POST
	 * @param array $options Options for the request
	 *
	 * @see AppHttp::request()
	 *
	 * @return string The result of the request
	 *
	 * @since 1.0.0
	 */
	public function post($url, $data = null, $options = array()) {
		return $this->request($url, array_merge(array('method' => 'POST', 'body' => $data), $options));
	}

	/**
	 * Execute a PUT http request
	 *
	 * @param string $url The url to PUT
	 * @param mixed $data The data to PUT
	 * @param array $options Options for the request
	 *
	 * @see AppHttp::request()
	 *
	 * @return string The result of the request
	 *
	 * @since 1.0.0
	 */
	public function put($url, $data = null, $options = array()) {
		return $this->request($url, array_merge(array('method' => 'PUT', 'body' => $data), $options));
	}

	/**
	 * Execute a general http request
	 *
	 * @param string $url The url to
	 * @param array $options Options for the request
	 *
	 * @return string The result of the request
	 *
	 * @since 1.0.0
	 */
	public function request($url, $options = array()) {

		if ($this->transport) {
			return $this->transport->request($url, $options);
		}

		return false;
	}

}

/**
 * Transport Class that uses CURL
 */
class AppHttpCurl extends AppHttp {

	/**
	 * Execute a general http request
	 *
	 * @param string $url The url to
	 * @param array $options Options for the request
	 *
	 * @return string The result of the request
	 *
	 * @since 1.0.0
	 */
	public function request($url, $options = array()) {

		// parse request
		$request = $this->_parseRequest($url, $options);

		// set curl options
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, $request['version'] == '1.0' ? CURL_HTTP_VERSION_1_0 : CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $request['timeout']);
		curl_setopt($curl, CURLOPT_TIMEOUT, $request['timeout']);
		curl_setopt($curl, CURLOPT_MAXREDIRS, $request['redirects']);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $request['ssl_verifypeer']);

		// post request ?
		if ($request['method'] == 'POST') {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request['body']);
		}

		// put request ?
		if ($request['method'] == 'PUT') {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request['method']);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request['body']);
		}

		// connect with curl
		$res = curl_exec($curl);
		curl_close($curl);

		// parse response
		$res = $this->_parseResponse($res);

		// save to file
		if ($res && $request['file'] && file_put_contents($request['file'], $res['body']) === false) {
			return false;
		}

		return $res;
	}

	/**
	 * Checks if CURL is available on the system
	 *
	 * @return boolean If CURL library is loaded
	 *
	 * @since 1.0.0
	 */
	public function available() {
		return function_exists('curl_init');
	}

}

/**
 * HTTP class that uses streams
 */
class AppHttpStreams extends AppHttp {

	/**
	 * Execute a general http request
	 *
	 * @param string $url The url to
	 * @param array $options Options for the request
	 *
	 * @return string The result of the request
	 *
	 * @since 1.0.0
	 */
	public function request($url, $options = array()) {

		// parse request
		$request = $this->_parseRequest($url, $options);

		// create stream options
		$options = array('http' =>
			array('method' => $request['method'],
			  	  'protocol_version' => $request['version'],
				  'max_redirects' => $request['redirects'],
				  'timeout' => $request['timeout'],
				  'ignore_errors' => true,
				  'content' => $request['body']
				)
			);

		// create header string
		$options['http']['header'] = $this->_buildHeader($request['header']);
		if (!empty($request['cookies'])) {
			$options['http']['header'] .= $this->buildCookies($request['cookies']);
		}

		// connect with fopen and streams
		$res  = false;
	    $fp   = @fopen($url, 'r', false, stream_context_create($options));
		$res  = stream_get_contents($fp);
		$meta = stream_get_meta_data($fp);
		fclose($fp);

		// parse response
		$res = $this->_parseResponse((isset($meta['wrapper_data']) ? implode($this->line_break, $meta['wrapper_data']).$this->line_break.$this->line_break : null).$res);

		// save to file
		if ($res && $request['file'] && file_put_contents($request['file'], $res['body']) === false) {
			return false;
		}

		return $res;
	}

	/**
	 * Check if it's possible to fopen and fread streams and if streams are available
	 *
	 * @return If streams can be used
	 *
	 * @since 1.0.0
	 */
	public function available() {
		return function_exists('fopen') && function_exists('ini_get') && ini_get('allow_url_fopen') && !version_compare(PHP_VERSION, '5.0', '<');
	}

}

/**
 * HTTP class that uses socket connections
 */
class AppHttpSocket extends AppHttp {

	/**
	 * Execute a general http request
	 *
	 * @param string $url The url to
	 * @param array $options Options for the request
	 *
	 * @return string The result of the request
	 *
	 * @since 1.0.0
	 */
	public function request($url, $options = array()) {

		// parse request
		$request = $this->_parseRequest($url, $options);

		// set host
		$host = $request['url']['scheme'] == 'https' ? sprintf('ssl://%s', $request['url']['host']) : $request['url']['host'];

		// connect with fsockopen
		$res = false;
	    $fp  = @fsockopen($host, $request['url']['port'], $errno, $errstr, $request['url']['timeout']);
	    if ($fp !== false) {
	        @fwrite($fp, $request['raw']);
	        while (!feof($fp)) {
	            $res .= fgets($fp, 4096);
	        }
	        @fclose($fp);
	    }

		// parse response
		$res = $this->_parseResponse($res);

		// save to file
		if ($res && $request['file'] && file_put_contents($request['file'], $res['body']) === false) {
			return false;
		}

		return $res;
	}

	/**
	 * Checks if it's possible to open socket connections
	 *
	 * @return If it's possible to use sockets
	 *
	 * @since 1.0.0
	 */
	public function available() {
		return function_exists('fsockopen');
	}

}


/**
 * HTTP base class
 *
 * Based on HTTP Socket connection class (http://cakephp.org, Cake Software Foundation, Inc., MIT License)
 */
class AppHttp {

    /**
	 * Default values for the request
	 *
	 * @var array
	 * @since 1.0.0
	 */
	public $request = array(
		'method' => 'GET',
		'version' => '1.1',
		'timeout' => 5,
		'redirects' => 5,
		'line' => null,
		'file' => null,
		'header' => array('Connection' => 'close', 'User-Agent' => 'App'),
		'body' => '',
		'cookies' => array(),
		'auth' => array('method' => 'Basic', 'user' => null, 'pass' => null),
		'raw' => null
	);

	/**
	 * Default request responses variables
	 *
	 * @var array
	 * @since 1.0.0
	 */
	public $response = array(
		'header' => array(),
		'body' => '',
		'cookies' => array(),
		'status' => array('http-version' => null, 'code' => null, 'reason-phrase' => null),
		'raw' => array('status-line' => null, 'header' => null, 'body' => null, 'response' => null)
	);

	/**
	 * Line break to use
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $line_break = "\r\n";

	/**
	 * Build cookie headers for the request
	 *
	 * @param array $cookies An associative array that represents the list of cookies ( $name => array('value' => $value))
	 *
	 * @return string The header for the request
	 *
	 * @since 1.0.0
	 */
	public function buildCookies($cookies) {
		$header = array();
		foreach ($cookies as $name => $cookie) {
			$header[] = $name.'='.$this->_escapeToken($cookie['value'], array(';'));
		}
		$header = $this->_buildHeader(array('Cookie' => $header), 'pragmatic');
		return $header;
	}

	/**
	 * Parse the cookies in a header
	 *
	 * @param string $header The header of a request
	 *
	 * @return array The array of cookies parsed
	 *
	 * @since 1.0.0
	 */
	public function parseCookies($header) {

		if (!isset($header['Set-Cookie'])) {
			return false;
		}

		$cookies = array();
		foreach ((array) $header['Set-Cookie'] as $cookie) {
			if (strpos($cookie, '";"') !== false) {
				$cookie = str_replace('";"', "{__cookie_replace__}", $cookie);
				$parts  = str_replace("{__cookie_replace__}", '";"', explode(';', $cookie));
			} else {
				$parts = preg_split('/\;[ \t]*/', $cookie);
			}

			list($name, $value) = explode('=', array_shift($parts), 2);
			$cookies[$name] = compact('value');

			foreach ($parts as $part) {
				if (strpos($part, '=') !== false) {
					list($key, $value) = explode('=', $part);
				} else {
					$key = $part;
					$value = true;
				}

				$key = strtolower($key);
				if (!isset($cookies[$name][$key])) {
					$cookies[$name][$key] = $value;
				}
			}
		}

		return $cookies;
	}

	/**
	 * Parses the given http request url and options to build the http request string
	 *
	 * @param string $url The url of the request
	 * @param array $options A list of options for the request
	 *
	 * @return array An associative array representing the request
	 *
	 * @since 1.0.0
	 */
	protected function _parseRequest($url, $options = array()) {

		$request = array_merge($this->request, array('url' => $this->_parseUrl($url)), $options);

		$request['timeout']   = (int) ceil($request['timeout']);
		$request['redirects'] = (int) $request['redirects'];

		if (is_array($request['header'])) {
			$request['header'] = $this->_parseHeader($request['header']);
			$request['header'] = array_merge(array('Host' => $request['url']['host']), $request['header']);
		}

		if (isset($request['auth']['user'], $request['auth']['pass'])) {
			$request['header']['Authorization'] = $request['auth']['method'].' '.base64_encode($request['auth']['user'].':'.$request['auth']['pass']);
		}

		if (isset($request['url']['user'], $request['url']['pass'])) {
			$request['header']['Authorization'] = $request['auth']['method'].' '.base64_encode($request['url']['user'].':'.$request['url']['pass']);
		}

		if (!empty($request['body']) && !isset($request['header']['Content-Type'])) {
			$request['header']['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		if (!empty($request['body']) && !isset($request['header']['Content-Length'])) {
			$request['header']['Content-Length'] = strlen($request['body']);
		}

		if (empty($request['line'])) {
			$request['line'] = strtoupper($request['method']).' '.$request['url']['path'].(isset($request['url']['query']) ? '?'.$request['url']['query'] : ''). ' HTTP/' . $request['version'].$this->line_break;
		}

		$request['raw'] = $request['line'].$this->_buildHeader($request['header']);

		if (!empty($request['cookies'])) {
			$request['raw'] .= $this->buildCookies($request['cookies']);
		}

		if (!isset($request['ssl_verifypeer'])) {
			$request['ssl_verifypeer'] = true;
		}

		$request['raw'] .= $this->line_break.$request['body'];

		return $request;
	}

	/**
	 * Parses the given http response and breaks it down in parts
	 *
	 * @param mixed The response
	 *
	 * @return array An associative array representing the response
	 *
	 * @since 1.0.0
	 */
	protected function _parseResponse($res) {

		// set defaults
		$response = $this->response;
		$response['raw']['response'] = $res;

		// parse header
		if (preg_match("/^(.+\r\n)(.*)(?<=\r\n)\r\n/Us", $res, $match)) {

			list($null, $response['raw']['status-line'], $response['raw']['header']) = $match;
			$response['raw']['body'] = substr($res, strlen($match[0]));

			if (preg_match("/(.+) ([0-9]{3}) (.+)\r\n/DU", $response['raw']['status-line'], $match)) {
				$response['status']['http-version'] = $match[1];
				$response['status']['code'] = (int) $match[2];
				$response['status']['reason-phrase'] = $match[3];
			}

			$response['header'] = $this->_parseHeader($response['raw']['header']);
			$response['body']   = $response['raw']['body'];

			if (!empty($response['header'])) {
				$response['cookies'] = $this->parseCookies($response['header']);
			}

		} else {
			$response['body'] = $res;
			$response['raw']['body'] = $res;
		}

		if (isset($response['header']['Transfer-Encoding']) && $response['header']['Transfer-Encoding'] == 'chunked') {
			$response['body'] = $this->_decodeChunkedBody($response['body']);
		}

		foreach ($response['raw'] as $field => $val) {
			if ($val === '') {
				$response['raw'][$field] = null;
			}
		}

		return $response;
	}

	/**
	 * Build the header for a request
	 *
	 * @param array|string $header The header of the request
	 * @param string $mode The mode of the header (default: standard)
	 *
	 * @return string The header of the request
	 *
	 * @since 1.0.0
	 */
	protected function _buildHeader($header, $mode = 'standard') {

		if (is_string($header)) {
			return $header;
		} elseif (!is_array($header)) {
			return false;
		}

		$returnHeader = '';
		foreach ($header as $field => $contents) {

			if (is_array($contents) && $mode == 'standard') {
				$contents = implode(',', $contents);
			}

			foreach ((array) $contents as $content) {
				$contents = preg_replace("/\r\n(?![\t ])/", "\r\n ", $content);
				$field = $this->_escapeToken($field);
				$returnHeader .= $field.': '.$contents.$this->line_break;
			}
		}

		return $returnHeader;
	}

	/**
	 * Parse a string based header into an array
	 *
	 * @param string $header The header
	 *
	 * @return array An array representing the header
	 *
	 * @since 1.0.0
	 */
	protected function _parseHeader($header) {

		if (is_array($header)) {
			foreach ($header as $field => $value) {
				unset($header[$field]);
				$field = strtolower($field);
				preg_match_all('/(?:^|(?<=-))[a-z]/U', $field, $offsets, PREG_OFFSET_CAPTURE);

				foreach ($offsets[0] as $offset) {
					$field = substr_replace($field, strtoupper($offset[0]), $offset[1], 1);
				}
				$header[$field] = $value;
			}
			return $header;
		} elseif (!is_string($header)) {
			return false;
		}

		preg_match_all("/(.+):(.+)(?:(?<![\t ])" . $this->line_break . "|\$)/Uis", $header, $matches, PREG_SET_ORDER);

		$header = array();
		foreach ($matches as $match) {
			list(, $field, $value) = $match;

			$value = trim($value);
			$value = preg_replace("/[\t ]\r\n/", "\r\n", $value);

			$field = $this->_unescapeToken($field);

			$field = strtolower($field);
			preg_match_all('/(?:^|(?<=-))[a-z]/U', $field, $offsets, PREG_OFFSET_CAPTURE);
			foreach ($offsets[0] as $offset) {
				$field = substr_replace($field, strtoupper($offset[0]), $offset[1], 1);
			}

			if (!isset($header[$field])) {
				$header[$field] = $value;
			} else {
				$header[$field] = array_merge((array) $header[$field], (array) $value);
			}
		}

		return $header;
	}

	/**
	 * Decode a chunked message body
	 *
	 * @param string $body The chunked message body
	 *
	 * @return string The decoded message
	 *
	 * @since 1.0.0
	 */
	protected function _decodeChunkedBody($body) {

		if (!is_string($body)) {
			return false;
		}

		$decodedBody = null;
		$chunkLength = null;

		while ($chunkLength !== 0) {

			// body is not chunked or is malformed
			if (!preg_match("/^([0-9a-f]+) *(?:;(.+)=(.+))?\r\n/iU", $body, $match)) {
				return $body;
			}

			$chunkSize = 0;
			$hexLength = 0;
			$chunkExtensionName = '';
			$chunkExtensionValue = '';
			if (isset($match[0])) {
				$chunkSize = $match[0];
			}
			if (isset($match[1])) {
				$hexLength = $match[1];
			}
			if (isset($match[2])) {
				$chunkExtensionName = $match[2];
			}
			if (isset($match[3])) {
				$chunkExtensionValue = $match[3];
			}

			$body = substr($body, strlen($chunkSize));
			$chunkLength = hexdec($hexLength);
			$chunk = substr($body, 0, $chunkLength);
			$decodedBody .= $chunk;

			if ($chunkLength !== 0) {
				$body = substr($body, $chunkLength + strlen("\r\n"));
			}
		}

		return $decodedBody;
	}

	/**
	 * Parse an URL and return its parts as an array
	 *
	 * @param string $url The url to parse
	 *
	 * @return array The parts of the url
	 *
	 * @since 1.0.0
	 */
	protected function _parseUrl($url) {

		// parse url
		$url = array_merge(array('user' => null, 'pass' => null, 'path' => '/', 'query' => null, 'fragment' => null), parse_url($url));

		// set scheme
		if (!isset($url['scheme'])) {
			$url['scheme'] = 'http';
		}

		// set host
		if (!isset($url['host'])) {
			$url['host'] = $_SERVER['SERVER_NAME'];
		}

		// set port
		if (!isset($url['port'])) {
			$url['port'] = $url['scheme'] == 'https' ? 443 : 80;
		}

		// set path
		if (!isset($url['path'])) {
			$url['path'] = '/';
		}

		return $url;
	}

	/**
	 * Escape a given token according to RFC 2616 (HTTP 1.1 specs)
	 *
	 * @param string $token The token to escape
	 * @param array $chars The chars to escape
	 *
	 * @return string The escaped token
	 *
	 * @since 1.0.0
	 */
	protected function _escapeToken($token, $chars = null) {
		$regex = '/(['.join('', $this->_tokenEscapeChars(true, $chars)).'])/';
		$token = preg_replace($regex, '"\\1"', $token);
		return $token;
	}

	/**
	 * Unescape a given token according to RFC 2616 (HTTP 1.1 specs)
	 *
	 * @param string $token The token to unescape
	 * @param array $chars The characters to unescape
	 *
	 * @return string The unescaped token
	 *
	 * @since 1.0.0
	 */
	protected function _unescapeToken($token, $chars = null) {
		$regex = '/"(['.join('', $this->_tokenEscapeChars(true, $chars)).'])"/';
		$token = preg_replace($regex, '\\1', $token);
		return $token;
	}

	/**
	 * Get escape chars according to RFC 2616 (HTTP 1.1 specs)
	 *
	 * @param boolean $hex If we have to use the hex codification
	 * @param array $chars An alternative list of characters to escape
	 *
	 * @return array The list of escaped characters
	 *
	 * @since 1.0.0
	 */
	protected function _tokenEscapeChars($hex = true, $chars = null) {

		if (!empty($chars)) {
			$escape = $chars;
		} else {
			$escape = array('"', "(", ")", "<", ">", "@", ",", ";", ":", "\\", "/", "[", "]", "?", "=", "{", "}", " ");
			for ($i = 0; $i <= 31; $i++) {
				$escape[] = chr($i);
			}
			$escape[] = chr(127);
		}

		if ($hex == false) {
			return $escape;
		}

		foreach ($escape as $key => $char) {
			$escape[$key] = '\\x'.str_pad(dechex(ord($char)), 2, '0', STR_PAD_LEFT);
		}

		return $escape;
	}

}